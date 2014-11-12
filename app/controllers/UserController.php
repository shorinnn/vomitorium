<?php
/*
|--------------------------------------------------------------------------
| Confide Controller Template
|--------------------------------------------------------------------------
|
| This is the default Confide controller template for controlling user
| authentication. Feel free to change to your needs.
|
*/

class UserController extends BaseController {
    public $meta;
    
    /**
        * Instantiate a new UserController instance.
        */
       public function __construct()
       {
           $this->beforeFilter('auth', array('only'=>array('upload_avatar','settings','logout')));
       }
       
    /**
     * Displays the form for account creation
     *
     */
    public function create($hash='')
    {
        $this->meta['pageTitle'] = 'Register';
        $this->meta['header_img_text'] = 'Register';    
        $this->meta['hash'] = '';
        $check_hash = str_replace('-trial','', $hash);
        if(PaymentPlan::whereRaw('SHA1(CONCAT("'.sys_settings().'",id)) = "'.$check_hash.'"')->count() >0){
            $this->meta['hash'] = $hash;
        }
        $exp = 60 * 24 * 90;
        if(Input::has('a')){
            Cookie::queue('affiliate',Input::get('a'),$exp);
        }
        if(Input::has('t')){
            Cookie::queue('tracking',Input::get('t'),$exp);
        }
        return View::make('pages.user_forms')->withForm(Confide::makeSignupForm()->withMeta($this->meta)->render())->withMeta($this->meta);
    }
    
    public function access_pass($hash=''){
        $this->meta['pageTitle'] = 'Register';
        $this->meta['header_img_text'] = 'Register';    
        $this->meta['hash'] = '';
        if(Code::where('code', $hash)->whereNull('used_by')->get()->count() > 0){
            $this->meta['hash'] = $hash;
            Session::set('accesspass',$hash);
        }
        return View::make('pages.user_forms')->withForm(Confide::makeSignupForm()->withMeta($this->meta)->render())->withMeta($this->meta);
    }

    /**
     * Stores new account
     *
     */
    public function store()
    {
        $user = new User;

        $user->first_name = Input::get( 'first_name' );
        $user->last_name = Input::get( 'last_name' );
        $user->username = Input::get( 'username' );
        $user->email = Input::get( 'email' );
        $user->password = Input::get( 'password' );
        $user->confirmed = 1;
        if(Cookie::has('affiliate')) $user->affiliate_id = Cookie::get('affiliate');
        if(Cookie::has('tracking')) $user->tracking_id = Cookie::get('tracking');
        $hash = Input::get('program_id'); // this is payment plan ID actually
 
        $user->password_confirmation = Input::get( 'password_confirmation' );
        
        // program validation
        $error = '';
        $no_program = false;
        if(Session::has('accesspass')){
            $code = Code::where('code', Session::get('accesspass'))->whereNull('used_by')->first();
            if($code==null) $error = "Invalid access pass";
        }
        if($error!=''){
            //return Redirect::action('UserController@create', array('hash'=>'asdYY'))->withInput()->with( 'error', $error );
            if(Session::has('accesspass')) return Redirect::to("register/accesspass/$hash")->withInput()->with( 'error', $error );
            else return Redirect::to("register/$hash")->withInput()->with( 'error', $error );
        }
        

        // Save if valid. Password field will be hashed before save
        $user->save();

        if ( $user->id )
        {
            Cookie::forget('affiliate');
            Cookie::forget('tracking');
            $notice = Lang::get('confide::confide.alerts.account_created') . ' ' . Lang::get('confide::confide.alerts.instructions_sent'); 
            // Assign user to Member role
            if(Input::has('role')){
                $user->confirmed = 1;
                $user->attachRole(Input::get('role'));
            }
            else $user->attachRole(2);
            
            if(Input::get('program_id')!='' && isset($code)){
                Session::set('payment_plan_id',Input::get('program_id'));
//                if(strlen(Input::get('program_id'))==16){//unique registration code
//                    $code = Code::where('code',Input::get('program_id'))->first();
//                    $code->used_by = $user->id;
//                    $code->used_at = date('Y-m-d H:i:s');
//                    $code->updateUniques();
//                    if(!$no_program){
//                        $data['user_id'] = $user->id;
//                        $data['program_id'] = $code->program_id;
//                        DB::table('programs_users')->insert($data);
//                    }
//                }
//                else{// program registration code
//                    if(!$no_program){
//                        $data['user_id'] = $user->id;
//                        $program_id = Program::whereRaw('SHA1(CONCAT("'.sys_settings().'",id)) = "'.Input::get('program_id').'"')->first()->id;
//                        $data['program_id'] = $program_id;
//                        DB::table('programs_users')->insert($data);
//                    }
//                }
            }
            $this->do_login();
            if(isset($code)){// user has access pass, no payment, associate with the program and log in
                $code->used_by = $user->id;
                $code->updateUniques();
                $data = array();
                $data['program_id'] = $code->program_id;
                $data['user_id'] = $user->id;
                $data['start_date'] = date('Y-m-d H:i:s');
                DB::table('programs_users')->insert($data);
                Session::set('program_id', $code->program_id);
                Session::forget('accesspass');
                Session::forget('payment_plan_id');
                Session::forget('trial');
                return Redirect::to("/");
            }
            else return Redirect::to("payment");
        }
        else
        {
            // Get validation errors (see Ardent package)
            $error = $user->errors()->all(':message');

                //return Redirect::action('UserController@create')->withInput(Input::except('password'))->with( 'error', $error );
             if(Session::has('accesspass')) return Redirect::to("register/accesspass/$hash")->withInput(Input::except('password'))->with( 'error', $error );
             else return Redirect::to("register/$hash")->withInput(Input::except('password'))->with( 'error', $error );
        }
    }

    /**
     * Displays the login form
     *
     */
    public function login()
    {
        if( Confide::user() )
        {
            // If user is logged, redirect to internal 
            // page, change it to '/admin', '/dashboard' or something
            return Redirect::to('/');
        }
        else
        {
            $this->meta['pageTitle'] = 'Login';
            $this->meta['header_img_text'] = 'Login';
            return View::make('pages.user_forms')->withForm(Confide::makeLoginForm()->render())->withMeta($this->meta);
        }
    }

    /**
     * Attempt to do login
     *
     */
    public function do_login()
    {
        $input = array(
            'email'    => Input::get( 'email' ), // May be the username too
            'username' => Input::get( 'email' ), // so we have to pass both
            'password' => Input::get( 'password' ),
            'remember' => Input::get( 'remember' ),
        );

        // If you wish to only allow login from confirmed users, call logAttempt
        // with the second parameter as true.
        // logAttempt will check if the 'email' perhaps is the username.
        // Get the value from the config file instead of changing the controller
        if ( Confide::logAttempt( $input, Config::get('confide::signup_confirm') ) ) 
        {
            if(Input::has('tz')) Session::set('tz', Input::get('tz'));
            // Redirect the user to the URL they were trying to access before
            // caught by the authentication filter IE Redirect::guest('user/login').
            // Otherwise fallback to '/'
            // Fix pull #145
            if(!admin()){
                if(Auth::user()->programs()!=false){
                   Session::set('program_id', Auth::user()->programs()[0]->id);
                }
                else  return Redirect::intended('/');
            }
            $state = json_decode(Auth::user()->state);
            if(isset($state->last_program)){
                Session::set('program_id',$state->last_program);
            }
            return Redirect::intended('/'); // change it to '/admin', '/dashboard' or something
        }
        else
        {
            $user = new User;

            // Check if there was too many login attempts
            if( Confide::isThrottled( $input ) )
            {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            }
            elseif( $user->checkUserExists( $input ) and ! $user->isConfirmed( $input ) )
            {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            }
            else
            {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

                        return Redirect::action('UserController@login')
                            ->withInput(Input::except('password'))
                ->with( 'error', $err_msg );
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string  $code
     */
    public function confirm( $code )
    {
        if ( Confide::confirm( $code ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
                        return Redirect::action('UserController@login')
                            ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
                        return Redirect::action('UserController@login')
                            ->with( 'error', $error_msg );
        }
    }

    /**
     * Displays the forgot password form
     *
     */
    public function forgot_password()
    {
        $this->meta['pageTitle'] = 'Forgot Password';
        return View::make('pages.user_forms')->withForm(Confide::makeForgotPasswordForm()->render())->withMeta($this->meta);
    }

    /**
     * Attempt to send change password link to the given email
     *
     */
    public function do_forgot_password()
    {
        if( Confide::forgotPassword( Input::get( 'email' ) ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
                        return Redirect::action('UserController@login')
                            ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
                        return Redirect::action('UserController@forgot_password')
                            ->withInput()
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Shows the change password form with the given token
     *
     */
    public function reset_password( $token =0)
    {
        $this->meta['pageTitle'] = 'Reset Password';
       return View::make('pages.user_forms')->withForm(Confide::makeResetPasswordForm($token)->render())->withMeta($this->meta);
    }

    /**
     * Attempt change password of the user
     *
     */
    public function do_reset_password()
    {
        $input = array(
            'token'=>Input::get( 'token' ),
            'password'=>Input::get( 'password' ),
            'password_confirmation'=>Input::get( 'password_confirmation' ),
        );

        // By passing an array with the token, password and confirmation
        if( Confide::resetPassword( $input ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
                        return Redirect::action('UserController@login')
                            ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
                        return Redirect::action('UserController@reset_password', array('token'=>$input['token']))
                            ->withInput()
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Log the user out of the application.
     *
     */
    public function logout()
    {
        Session::flush();
        Confide::logout();
        return Redirect::to('/');
    }
    
     public function settings(){
            $meta['header_img_text'] = 'Profile Settings';
            return View::make('pages.settings')->with('pageTitle','Settings')->withMeta($meta);
        }
        
        public function upload_avatar(){
            $installation = sys_settings('installation');
            
            $name = $installation.'-profile'.Str::random();
            $file = Input::file('file');
            $filename = $name.'.'.$file->getClientOriginalExtension(); 
            $allowed = array('jpg','png','gif');
            if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)) return 'error';
            // delete the old avatar
            if(Auth::user()->avatar!=''){
                @unlink(base_path().'/assets/img/avatars/'.Auth::user()->avatar);
            }
            
            $destination = base_path().'/assets/img/avatars/';
            
            $image = new SimpleImage(); 
            $image->load($_FILES['file']['tmp_name']); 
            $image->resizeToHeight(156); 
            $upload_success = $image->save($destination.$filename); 
            if( $upload_success ) {
                Auth::user()->avatar = $filename;
                Auth::user()->updateUniques();
                return url('assets/img/avatars/'.$filename);
            } else {
               return 'error';
            }
        }
        
        public function subscriptions(){
            $meta['header_img_text'] = 'My Subscriptions';
            $subscriptions = DB::table('programs_users')->whereNull('subscription_cancelled')->where('user_id', Auth::user()->id)->get();
            return View::make('pages.subscriptions')->withMeta($meta)->withSubscriptions($subscriptions);
        }
        
        public function cancel_subscription(){
            if(Input::get('provider')=='stripe') return Stripe_processor::cancel_subscription(Input::get('id'));
            else return Paypal_processor::cancel_subscription(Input::get('id'));
        }

}
