<?php

class AdminController extends BaseController {

    public function __construct() {
        $this->beforeFilter('admin', array('except'=>array('edit_remark')));
    }

    public function new_submissions() {
        $new_submissions = UserManager::new_submissions();
        if (Request::ajax()) {
            return View::make('admin.admin_submissions_partial')
                            ->with('pageTitle', 'New Submissions')->withSubmissions($new_submissions);
        }
        return View::make('admin.admin_submissions')
                        ->with('pageTitle', 'New Submissions')->withSubmissions($new_submissions);
    }

    public function unattended_submissions() {
        $new_submissions = UserManager::unattended_submissions();
        if (Request::ajax()) {
            return View::make('admin.admin_submissions_partial')
                            ->with('pageTitle', 'New Submissions')->withSubmissions($new_submissions);
        }
        return View::make('admin.admin_submissions')
                        ->with('pageTitle', 'Unattended Submissions')->withSubmissions($new_submissions);
    }

    public function new_messages() {
        $comments = UserManager::new_admin_comments();
        if (Request::ajax()) {
            return View::make('admin.admin_comments_partial')
                            ->with('pageTitle', 'New Comments')->withComments($comments);
        }
        return View::make('admin.admin_comments')
                        ->with('pageTitle', 'New Comments')->withComments($comments);
    }

    public function unattended_messages() {
        $comments = UserManager::unattented_comments();
        if (Request::ajax()) {
            return View::make('admin.admin_comments_partial')
                            ->with('pageTitle', 'Unattended Comments')->withComments($comments);
        }
        return View::make('admin.admin_comments')
                        ->with('pageTitle', 'Unattended Comments')->withComments($comments);
    }
    
    public function user_page($id){
        $user = User::find($id);
        $meta['header_img_text'] = "$user->first_name $user->last_name ($user->username)";
        $lessons = Lesson::where('program_id', Session::get('program_id'))->orderBy('chapter_ord','asc')->orderBy('ord','asc')->with('chapter')->get();
        return View::make('admin.user')->with('pageTitle', $user->username."'s page")->withUser($user)->withLessons($lessons)->withMeta($meta);
    }
    
    public function mark_attended(){
        //Answer_comment::mark(Input::get('message'), Input::get('block_id'),'attended',1);
        //Conversation::where('id', Input::get('message'))->update(array('attended'=>1));
        $c = Conversation::where('id', Input::get('message'))->first();
        $c->timestamps = false;
        $c->attended = 1;
        $c->save();
        return;
    }
    
    
    public function mark_unattended(){
        //Answer_comment::mark(Input::get('message'), Input::get('block_id'), 'attended', 0);
        //Conversation::where('id', Input::get('message'))->update(array('attended'=>0));
        $c = Conversation::where('id', Input::get('message'))->first();
        $c->timestamps = false;
        $c->attended = 0;
        $c->save();
        return;
    }
    
     public function mark_submission_attended(){
        Block_answer::mark(Input::get('message'),'attended',1);
        return;
    }
    
    
    public function mark_submission_unattended(){
        Block_answer::mark(Input::get('message'), 'attended', 0);
        return;
    }
    
    public function mark_lesson(){
        Block_answer::mark_lesson(Input::get('lesson'), Input::get('user'));
        return;
    }
    
    public function mark_user(){
        $blocks = Block_answer::where('user_id',Input::get('user'))->get();
        foreach($blocks as $b){
            $b->attended = 1;
            $b->timestamps = false;
            $b->save();
        }
        $comments = Conversation::where('user_id',Input::get('user'))->where('posted_by','user')->get();
        foreach($comments as $c){
           $c->attended = 1;
           $c->timestamps = false;
           $c->save();
        }
        return;
    }
    
    public function post_remark(){
        $remark = new Conversation();
        $remark->content = Input::get('remark');
        $remark->user_id = Input::get('user');
        $remark->lesson_id = Input::get('lesson');
        $remark->admin_id = Auth::user()->id;
        $remark->posted_by = 'admin';
        $remark->attended = 1;
        $remark->read = 0;
        $remark->save();
        
        $ids = json_decode(Input::get('attachments'));
        if(count($ids)>0){
            Attachment::whereIn('id', $ids)->update(array('conversation_id'=>$remark->id));
        }
        $response['html'] = View::make('pages.lesson.remarks')->with('remarks',array($remark))->render();
        $response['id'] = $remark->id;
        return json_encode($response);
        
    }
    
    public function edit_remark(){
        if(admin()){
            //$remark = Remark::where('admin_id', Auth::user()->id)->where('id', Input::get('id'))->first();
            $remark = Conversation::where('admin_id', Auth::user()->id)->where('id', Input::get('id'))->first();
        }
        else{
            //$remark = Remark::where('user_id', Auth::user()->id)->where('id', Input::get('id'))->first();
            $remark = Conversation::where('user_id', Auth::user()->id)->where('id', Input::get('id'))->first();
        }
        $remark->content = Input::get('text');
        $remark->save();
    }
    public function appearance(){
        $meta['header_img_text'] = 'Appearance';
        return View::make('admin.appearance')->with('pageTitle', "Appearance")->withMeta($meta);
    }
    
    public function system_settings(){
        $meta['header_img_text'] = 'System Settings';
        return View::make('admin.system_settings')->with('pageTitle', "System Settings")->withMeta($meta);
    }
    
    public function update_system_settings(){
        if($_POST['name']=='installation') return;
        DB::table('settings')->update(array($_POST['name'] => trim($_POST['value'])));
        if(in_array($_POST['name'], array('custom_color_1','custom_color_2','custom_color_3','custom_color_4','custom_color_5','custom_color_6','tagline_background_color'))){
            $css = file_get_contents(  base_path().'/assets/css/style.css' );
            $custom = file_get_contents(  base_path().'/assets/css/custom.css' );
            $lesson = file_get_contents(  base_path().'/assets/css/lesson.css' );
            $color1 = sys_settings('custom_color_1');
            $color2 = sys_settings('custom_color_2');
            $color3 = sys_settings('custom_color_3');
            $color4 = sys_settings('custom_color_4');
            $color5 = sys_settings('custom_color_5');
            $color6 = sys_settings('custom_color_6');
            if($color1!=''){
                $css = str_replace('#1e6e37', $color1, $css); 
                $custom = str_replace('#1e6e37',$color1, $custom); 
                $lesson = str_replace('#1e6e37',$color1, $lesson); 
            }
            if($color2!=''){
                $css = str_replace('#33443e',$color2, $css); 
                $custom = str_replace('#33443e',$color2, $custom); 
                $lesson = str_replace('#33443e',$color2, $lesson); 
            }
            if($color3!=''){
                $css = str_replace('#52b963',$color3, $css); 
                $custom = str_replace('#52b963',$color3, $custom); 
                $lesson = str_replace('#52b963',$color3, $lesson); 
            }
            if($color4!=''){
                $css = str_replace('#489C56', $color4, $css); 
                $custom = str_replace('#489C56', $color4, $custom); 
                $lesson = str_replace('#489C56', $color4, $lesson); 
            }
            if($color5!=''){
                $css = str_replace('#78e9f1', $color5, $css); 
                $custom = str_replace('#78e9f1', $color5, $custom); 
                $lesson = str_replace('#78e9f1', $color5, $lesson); 
            }
            if($color6!=''){
                $css = str_replace('#f8f8f8', $color6, $css); 
                $custom = str_replace('#f8f8f8', $color6, $custom); 
                $lesson = str_replace('#f8f8f8', $color6, $lesson); 
            }
                    $css = $css.$custom.$lesson;
            $filename = base_path()."/assets/stylesheets/stylesheet".sys_settings().'.css';
            file_put_contents($filename, $css);
        }
    }
    
    public function background(){
        $installation = sys_settings('installation');
        
        $name = $installation.'-bg'.Str::random();
        $file = Input::file('file');
        $filename = $name.'.'.$file->getClientOriginalExtension(); 
        $allowed = array('jpg','png','gif');
        if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)) return 'error';
        // delete the background
        @unlink(base_path().'/assets/img/backgrounds/'.sys_settings('bgimage'));
        
        $destination = base_path().'/assets/img/backgrounds/';

        $image = new SimpleImage(); 
        $image->load($_FILES['file']['tmp_name']); 
        $upload_success = $image->save($destination.$filename); 
        if( $upload_success ) {
            DB::table('settings')->update(array('bgimage' => $filename));
            return url('assets/img/backgrounds/'.$filename);
        } else {
           return 'error';
        }
    }
    
    public function logo(){
        $installation = sys_settings('installation');
        
        $name = $installation.'-logo'.Str::random();
        $file = Input::file('file');
        $filename = $name.'.'.$file->getClientOriginalExtension(); 
        $allowed = array('jpg','png','gif');
        if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)) return 'error';
        // delete the background
        @unlink(base_path().'/assets/img/logos/'.sys_settings('logo'));
        
        $destination = base_path().'/assets/img/logos/';

        $upload_success = Input::file('file')->move($destination, $filename);
        if( $upload_success ) {
            DB::table('settings')->update(array('logo' => $filename));
            return url('assets/img/logos/'.$filename);
        } else {
           return 'error';
        }
    }
    
    public function mark_remark_attended(){
        $c = Conversation::where('id',Input::get('message'))->first();
        $c->attended = 1;
        $c->timestamps = false;
        $c->save();
    }

}
