<?php

class UserManagerController extends BaseController {
    public $meta;
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->beforeFilter('admin');
    }


    /**
     * Displays the form for account creation
     *
     */
    public function index(){  
        $this->meta['header_img_text'] = $this->meta['pageTitle'] = 'Clients';
        $this->meta['javascripts'] = array('../assets/js/admin/userManager.js');
        $members = DB::table('assigned_roles')->where('role_id',2)->lists('user_id');
        if(count($members)==0) $members = array(0);
        $users = User::whereIn('id',$members)->paginate(15);        
        //$users = User::paginate(15);        
        $roles = editable_json(Role::get(array('id','name')));
        
        if(Request::ajax()){
            return View::make('user_manager.users')->withUsers($users)->withRoles($roles);
        }
        $programs = get_programs();
        return View::make('user_manager.index')->withMeta($this->meta)->withUsers($users)->withRoles($roles)->withPrograms($programs);
    }
    
    public function update(){
        return UserManager::update_field(Input::all());
    }
    
    public function destroy($id=0){
        return UserManager::destroy($id);
    }
    
    public function change_password($id=0){
        return View::make('user_manager.change_password')->withId($id);
    }
    
    public function do_change_password(){
        return UserManager::change_password(Input::all());
    }
    
    public function search(){
        $users = User::where('username', 'like', Input::get('search').'%')->paginate(15);     
        $roles = editable_json(Role::get(array('id','name')));
        return View::make('user_manager.users')->withUsers($users)->withRoles($roles)->withIs_search(true);
    }
    
    public function assign_coach($id){
        $user = User::find($id);
        $admins = array();
        foreach(User::all() as $u){
            if($u->hasRole("Admin")) $admins[] = $u;
        }
        $relations = $user->get_assignments();
        $meta['header_img_text'] = $meta['pageTitle'] = "Assign Coach to $user->first_name $user->last_name ($user->username)";
        return View::make('user_manager.assign_coach')->withUser($user)->withMeta($meta)->withAdmins($admins)->withRelations($relations);
    }
    
    public function do_assign_coach($id){
        $user = User::find($id);
        if( $user->update_admins(Input::all())){
            $response['status'] = 'success';
            $response['text'] = 'Update successful';
        }
        else{
            $response['status'] = 'error';
            $response['text'] = 'An error occurred';
        }

        return json_encode($response);
    }
    
    public function chat_permissions($id){
        $user = User::find($id);
        $admins = array();
        foreach(User::all() as $u){
            if($u->hasRole("Admin")) $admins[] = $u;
        }
        $relations = $user->get_assignments();
        $meta['header_img_text'] = $meta['pageTitle'] = "Set Group Chat Permissions For $user->first_name $user->last_name ($user->username)";
        return View::make('user_manager.group_chat')->withUser($user)->withMeta($meta);
    }
    
    public function set_chat_permissions($id){
        $user = User::find($id);
        foreach(Input::get('chat_permissions') as $program => $permission){
           DB::table('programs_users')->where('program_id',$program)->where('user_id',$id)->update( ['group_conversations' => $permission] );
           $permission = Input::get('coach_chat_permissions')[$program];
           DB::table('programs_users')->where('program_id',$program)->where('user_id',$id)->update( ['coach_conversations' => $permission] );
        }
        $meta['header_img_text'] = $meta['pageTitle'] = "Set Group Chat Permissions For $user->first_name $user->last_name ($user->username)";
        $response['status'] = 'success';
        $response['text'] = 'Update successful';
        return json_encode($response);
    }
    
    public function register(){
        if(Input::get('type')=='link'){
            $data = Input::all();
            $plan = PaymentPlan::find(Input::get('payment_plan'));
            
            $data['program_name'] = $plan->program->name;
            $hash = sha1(sys_settings().Input::get('program'));
            //$data['link'] = url('register/'.$hash);
            $data['link'] = url('register').'/'.sha1(sys_settings().$plan->id);
            if(sys_settings('send_registration_email')!=''){
                $content = sys_settings('send_registration_email');
                $content = str_replace('[FIRST_NAME]', Input::get('first_name'), $content);
                $content = str_replace('[LAST_NAME]', Input::get('last_name'), $content);
                $content = str_replace('[LINK]', $data['link'], $content);
                $content = str_replace('[PROGRAM_NAME]', $data['program_name'], $content);
                $data['content'] = $content;
            }
            Mail::send('emails.registration_link', $data, function($message){
                $message->to(Input::get('email'), Input::get('first_name').' '.Input::get('last_name'))->subject('Registration Link');
            });
            $response['text'] = View::make('user_manager.link_emailed')->withEmail(Input::get('email'))->render();
            $response['callback'] = 'link_sent';
        }
        else if(Input::get('type')=='manual'){
            $user = new User;
            $user->first_name = Input::get( 'first_name' );
            $user->last_name = Input::get( 'last_name' );
            $user->username = Input::get( 'username' );
            $user->email = Input::get( 'email' );
            $user->password = Input::get( 'password' );
            $user->password_confirmation = Input::get( 'password' );
            $user->confirmed = 1;
            $user->save();
                if ( $user->id ){
                    $user->confirmed = 1;
                    $user->attachRole(Input::get('level'));
                    $data['user_id'] = $user->id;
                    $data['program_id'] = Input::get('program');
                    DB::table('programs_users')->insert($data);      
                    
                    if(Input::get('send_email')==1){
                        $data = Input::all();
                        $data['program_name'] = Program::find(Input::get('program'))->name;
                        $data['link'] = url('login');
                        if(sys_settings('manual_registration_email')!=''){
                            $content = sys_settings('manual_registration_email');
                            $content = str_replace('[FIRST_NAME]', Input::get('first_name'), $content);
                            $content = str_replace('[LAST_NAME]', Input::get('last_name'), $content);
                            $content = str_replace('[LINK]', $data['link'], $content);
                            $content = str_replace('[PROGRAM_NAME]', $data['program_name'], $content);
                            $content = str_replace('[PASSWORD]', Input::get('password'), $content);
                            $data['content'] = $content;
                        }
                        Mail::send('emails.manual_registration', $data, function($message){
                            $message->to(Input::get('email'), Input::get('first_name').' '.Input::get('last_name'))->subject('Your Account');
                        });
                    }
                }
                else
                {
                   $response['status'] = 'danger';
                   $response['text'] = 'Cannot add this user: '.format_validation_errors($user->errors()->all());
                   return json_encode($response);
                }
            $response['callback'] = 'link_sent';
            $response['text'] =  View::make('user_manager.client_added')->withUser($user)->withPassword(Input::get('password'))
                    ->withEmailed(Input::get('send_email'))->render();
        }
        else if(Input::get('type')=='send_code'){
            $code = new Code();
//            $program = Program::find(Input::get('program'));
            $plan = PaymentPlan::find(Input::get('program'));
            $program = $plan->program;
            $data['program_name'] = $program->name;
            $code->program_id = $program->id;
            $code->payment_plan_id = $plan->id;
            do{
                $code->code = Str::random();
            }
            while(!$code->save());
            $data['link'] = url("register/accesspass/$code->code");
            if(sys_settings('send_code_email')!=''){
                $content = sys_settings('send_code_email');
                $content = str_replace('[FIRST_NAME]', Input::get('first_name'), $content);
                $content = str_replace('[LAST_NAME]', Input::get('last_name'), $content);
                $content = str_replace('[LINK]', $data['link'], $content);
                $content = str_replace('[PROGRAM_NAME]', $data['program_name'], $content);
                $data['content'] = $content;
            }
            Mail::send('emails.access_pass_link', $data, function($message){
                $message->to(Input::get('email'), Input::get('first_name').' '.Input::get('last_name'))->subject('Access Pass');
            });
            $response['text'] = View::make('user_manager.code_emailed')->withEmail(Input::get('email'))->render();
            $response['callback'] = 'link_sent';
        }
        else{
            $plan = PaymentPlan::find(Input::get('program'));
            $program = $plan->program;
            $codes = array();
            $code_string = array();
            for($i=0; $i<Input::get('code_count');++$i){
                $code = new Code();
                $code->program_id = $program->id;
                $code->payment_plan_id = $plan->id;
                do{
                    $code->code = Str::random();
                }
                while(!$code->save());
                $codes[] = $code;
                $code_string[] = url("register/accesspass/$code->code");
            }
            $code_string = implode(', ',$code_string);
            $response['callback'] = 'link_sent';
            $response['text'] = View::make('user_manager.codes')->withCodes($codes)->withCode_string($code_string)->render();
        }
        $response['status'] = 'success';
        return json_encode($response);
    }
    
    public function codes(){
        $codes = Code::paginate(3);
        $meta['header_img_text'] = 'Generated Codes';
        if(Request::ajax()){
            return View::make('user_manager.all_codes_partial')->withCodes($codes)->withMeta($meta);
        }
        return View::make('user_manager.all_codes')->withCodes($codes)->withMeta($meta);
    }
    
    public function comma_separated_codes(){
        $codes = Code::whereNull('used_at')->lists('code');
        foreach($codes as $key=>$code){
            $codes[$key] = url("register/accesspass/$code");
        }
        $codes = implode( ', ', $codes );
        $meta['header_img_text'] = 'Generated Codes';
        return View::make('user_manager.comma_separated_codes')->withCodes($codes)->withMeta($meta);
    }
    
    public function add_clients_ui(){
        $programs = get_programs();
        $plans = PaymentPlan::where('program_id', Session::get('program_id'))->get();
        return View::make('user_manager.add_clients')->withPrograms($programs)->withPlans($plans);
    }
}

