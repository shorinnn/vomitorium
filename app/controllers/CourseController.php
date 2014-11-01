<?php

class CourseController extends BaseController {

   public function __construct()
       {
           $this->beforeFilter('auth', array('except'=>array('lesson')));
       }
       
    public function lesson($slug = '',$user_id=0) {
        if(Auth::guest()) $lesson = Lesson::where('slug', $slug)->where('published', 1)->with('blocks')->first();
        else $lesson = Lesson::where('program_id',Session::get('program_id'))->where('slug', $slug)->where('published', 1)->with('blocks')->first();
        if($lesson==null) return Redirect::to('/');
        if(!admin()){
            // see if lesson has been released
            if($lesson->release_type=='on_date'){
                $meta['pageTitle'] = $meta['header_img_text'] = 'This lesson is not available yet';
                $date = strtotime($lesson->release_value);
                if(time() < $date) return View::make('pages.lesson.back_on_date')->withLesson($lesson)->withMeta($meta);
            }
            if($lesson->release_type=='after'){
                $meta['pageTitle'] = $meta['header_img_text'] = 'This lesson is not available yet';
                $start_date = DB::table('programs_users')->where('program_id', Session::get('program_id'))->where('user_id', Auth::user()->id)->first()->start_date;
                $release_date = strtotime("$start_date + $lesson->release_value day");
                $datetime1 = new DateTime( date("Y-m-d", $release_date) );
                $datetime2 = new DateTime();
                $interval = $datetime2->diff($datetime1)->format('%R%a');
                if((int) $interval > 0 || $interval ==='+0'){
                    $interval = (int) $interval + 1; 
                    return View::make('pages.lesson.back_in_days')->withDays($interval)->withMeta($meta);
                }
            }

        }
        Lesson::save_user_progress($lesson);
        $next_lesson = next_lesson($lesson);
        
        
        $next_unattended = '';
        $unattended = 0;
        $current_remark = ''; $unread_remark = 0;
        $current_user = null;
        
        if(admin() && $user_id>0){
            $current_user = User::find($user_id);
            Session::flash('user_id',$user_id);
            $unattended = UserManager::unattended_answers($user_id, $lesson->id) + UserManager::unattended_comments($user_id, $lesson->id)  + UserManager::unattended_remarks($user_id, $lesson->id);
            $next_unattended = UserManager::next_unattended($user_id, $lesson->id);
            $current_remark = Remark::where('user_id',$user_id)
                ->where('admin_id', Auth::user()->id)
                ->where('lesson_id',$lesson->id)->first();
            if($current_remark!=null) $current_remark = $current_remark->remark;
        }
        
        $personality = Skill::where('type', 'Personality Skills')->first()->values;
        $skills['personality'] = explode(',', $personality);
        $functional = Skill::where('type', 'Functional Skills')->first()->values;
        $skills['functional'] = explode(',', $functional);
        $total_lesson_remarks = 0;
        if(!Auth::guest()){
            if($user_id==0) $user_id = Auth::user()->id;
            $lessons = Lesson::where('program_id',$lesson->program_id)->lists('id');
            //$remarks = Remark::where('user_id', $user_id)->whereIn('lesson_id',$lessons)->orderBy('id','ASC')->get();
            $remarks = Conversation::where('user_id', $user_id)->whereIn('lesson_id',$lessons)->orderBy('id','ASC')->get();
            $unread_remark = Remark::where('user_id', $user_id)->where('lesson_id',$lesson->id)->where('read',0)->count();
            
            $lesson_remarks = Conversation::where('user_id', $user_id)->take(1)->where('lesson_id',$lesson->id)->orderBy('id','DESC')->get();
            $total_lesson_remarks = Conversation::where('user_id', $user_id)->where('lesson_id',$lesson->id)->count();
        }
        else{
            //$remarks = Remark::where('user_id',0)->where('lesson_id',$lesson->id)->orderBy('id','ASC')->get();
            $lesson_remarks = $remarks = Conversation::where('user_id',0)->where('lesson_id',$lesson->id)->orderBy('id','ASC')->get();
        }
        
        $this->meta['pageTitle'] = $lesson->title;
        $this->meta['pageKeywords'] = $lesson->meta_keywords;
        $this->meta['pageDescription'] = $lesson->meta_description;
        $this->meta['header_img_text'] = $lesson->chapter_id >0 ? $lesson->chapter->title : $lesson->title;
        $lesson_progress = $lesson->program_progress();
        return View::make('pages.lesson')->withMeta($this->meta)->withCurrent_user($current_user)->withRemarks($remarks)
                ->withCurrent_remark($current_remark)->withUnread_remark($unread_remark)->withLesson($lesson)->withSkills($skills)
                ->withUnattended($unattended)->withNext_unattended($next_unattended)->withLessonProgress($lesson_progress)
                ->withLesson_remarks($lesson_remarks)->withTotal_lesson_remarks($total_lesson_remarks);
    }

    public function store_answer($slug) {
        if (Auth::guest())return Redirect::to('/');
        
        $lesson = Lesson::where('slug', $slug)->where('published', 1)->first();
        $msg  = 'Answer Submitted!';
        Session::flash('success', $msg);
        Block_answer::store_answers(Input::all());
        if(Request::ajax()){
            $msg = Session::get('success');
            $next_lesson_btn = '';
            $next_lesson = next_lesson($lesson);          
            if($next_lesson!=''){
                $next_lesson_btn =  "<br /><br /><div class='text-center next-lesson-btn'>
            
                        <a class='btn btn-success' href='".URL('lesson/'.$next_lesson)."'><i class='glyphicon glyphicon-forward'></i>Go to next lesson</a>
                    </div>";
                $msg.=' Click the button below to go to the next lesson.';
            }
            Session::forget('success');
            $response['text'] = $msg;
            $response['button'] = $next_lesson_btn;
            return json_encode($response);
        }
        
        return $this->lesson($slug);
    }

    public function load_messages() {
        $comments = Conversation::where('block_answer_id', Input::get('answer_id'))->take(2)->skip(Input::get('skip'))->orderBy('id','desc')->get();
        $response['comments'] = View::make('pages.lesson.comments')->withComments($comments)->render();
        $response['remaining'] = count(Conversation::where('block_answer_id', Input::get('answer_id'))->take(2)->skip(Input::get('skip')+2)->get());
        return json_encode($response);
    }
    public function load_lesson_comments() {
        $remarks = Conversation::where('lesson_id', Input::get('lesson_id'))
                ->take(2)->skip(Input::get('skip'))->orderBy('id','desc')->get();
        $response['comments'] = View::make('pages.lesson.remarks')->withRemarks($remarks)->withReverse(1)->render();
        $response['remaining'] = count(Conversation::where('lesson_id', Input::get('lesson_id'))->take(2)->skip(Input::get('skip')+2)->get());
        return json_encode($response);
    }
        
    public function mark_read(){
        //Answer_comment::mark(Input::get('message'), Input::get('block_id'), 'read');
         Conversation::where('id', Input::get('message'))->where('block_answer_id',Input::get('block_id'))->update(array('attended'=>1));   
        return;
    }
    
    public function mark_remark_read(){
        Conversation::where('id',Input::get('message'))->where('user_id', Auth::user()->id)->update(array('read'=>1));
    }
    
    public function reply() {
        $c = new Conversation();
        $c->content = Input::get('reply_txt');
        $c->block_answer_id = Input::get('block_id');
        $response['marked_attended'] = 0;
        $c->admin_id = $c->user_id = 0;
        if (admin()){
            $c->attended = 1;
            $c->admin_id = Auth::user()->id;
            $c->user_id = Input::get('uid');
            Block_answer::where('id', Input::get('block_id'))->update(array('attended'=>1));
            $response['marked_attended'] = 1;
            $c->posted_by = 'admin';
        }
        else{
            $c->read = 1;
            $c->user_id = Auth::user()->id;
            $c->admin_id = Auth::user()->coach( Session::get('program_id') )->id;
            $c->posted_by = 'user';
        }
            
        if (!$c->save()){
            $response['status'] = 'error';
            $response['text'] = format_validation_errors($c->errors()->all());
            return json_encode($response);
        }
        
        $ids = json_decode(Input::get('attachments'));
        if(count($ids)>0){
            Attachment::whereIn('id', $ids)->update(array('conversation_id' => $c->id));
        }
        
        $response['text'] = View::make('pages.lesson.comments')->withComments(array($c))->withSingle_object(true)->render();
        $response['id'] = $c->id;
        $response['status'] = 'success';
        return json_encode($response);
    }

    public function edit_reply() {
        if(admin())$c = Conversation::where('admin_id', Auth::user()->id)->where('id', Input::get('id'))->first();
        else $c = Conversation::where('user_id', Auth::user()->id)->where('id', Input::get('id'))->first();
        if ($c == null) return 'error';
        
        $c->content = Input::get('txt');
        if(admin()) $c->read = 0;
        else $c->attended = 0;
        if (!$c->save())
        return 'error';
    }
    
    public function courses(){
        $courses = Lesson::user_courses(true);
        $meta ['header_img_text'] = 'Courses';
        return View::make('pages.courses')->withCourses($courses)->withMeta($meta);
    }
    
    public function dynamic_answers($cat_id){
        $lessons = Lesson::where('program_id', Session::get('program_id'))->where('published',1)->lists('id');
        $blocks = Block::where('category_id',$cat_id)->whereIn('lesson_id', $lessons)->where('type','!=','category')->get();
        return View::make('pages.lesson.dynamic_answer')->withBlocks($blocks)->render();
    }
    
    public function save_image(){
        $file_max = ini_get('upload_max_filesize');
        $file_max_str_leng = strlen($file_max);
        $file_max_meassure_unit = substr($file_max,$file_max_str_leng - 1,1);
        $file_max_meassure_unit = $file_max_meassure_unit == 'K' ? 'kb' : ($file_max_meassure_unit == 'M' ? 'mb' : ($file_max_meassure_unit == 'G' ? 'gb' : 'units'));
        $file_max = substr($file_max,0,$file_max_str_leng - 1);
        $file_max_byte = intval(toByteSize($file_max.' '.$file_max_meassure_unit));
            
        try{
            $file = Input::file('file');
            $file_size = intval(toByteSize($file->getSize().' B'));
            if ($file_size==0 || $file_size > $file_max_byte) return "--error--Your file size exceedes our current limit ($file_max $file_max_meassure_unit).";
            $allowed = array('jpg', 'jpeg', 'gif', 'png');
            $installation = sys_settings('installation');
            $name = Auth::user()->id.'-'.Str::random();

            $filename = $installation.'-'.$name.'.'.$file->getClientOriginalExtension(); 
            if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)){
                return '--error--This file type ('.$file->getClientOriginalExtension().') is not supported.';
            }

            $destination = base_path().'/assets/uploads/';
            $upload_success = Input::file('file')->move($destination, $filename);
            if( $upload_success ) {
                // delete old file
                $answer = Block_answer::where('user_id',Auth::user()->id)->where('block_id', Input::get('block'))->first();
                if($answer==null){
                    $answer = new Block_answer();
                    $answer->user_id = Auth::user()->id;
                    $answer->block_id = Input::get('block');                    
                }
                else{
                    $old = $answer->answer;
                    @unlink(base_path().'/assets/uploads/'.$old);
                }
                
                $answer->answer = $filename;
                $answer->save();
                return url('assets/uploads/'.$filename);
            } 
            else {
               return '--error--An unexpected error was encountered.';
            }
        }
        catch(Exception $e){
             $file = Input::file('file');
             $file_size = intval(toByteSize($file->getSize().' B'));
             if ($file_size > $file_max_byte) return "--error--Your file size exceedes our current limit ($file_max $file_max_meassure_unit)!";
        }
            
    }
    
    public function save_file(){
        $file_max = ini_get('upload_max_filesize');
        $file_max_str_leng = strlen($file_max);
        $file_max_meassure_unit = substr($file_max,$file_max_str_leng - 1,1);
        $file_max_meassure_unit = $file_max_meassure_unit == 'K' ? 'kb' : ($file_max_meassure_unit == 'M' ? 'mb' : ($file_max_meassure_unit == 'G' ? 'gb' : 'units'));
        $file_max = substr($file_max,0,$file_max_str_leng - 1);
        $file_max_byte = intval(toByteSize($file_max.' '.$file_max_meassure_unit));
            
        try{
            $file = Input::file('file');
            $file_size = intval(toByteSize($file->getSize().' B'));
            if ($file_size==0 || $file_size > $file_max_byte) return "--error--Your file size exceedes our current limit ($file_max $file_max_meassure_unit).";
            $allowed = array('jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'pdf', 'odt', 'zip');
            $installation = sys_settings('installation');
            $name = Auth::user()->id.'-'.Str::random();

            $filename = $installation.'-'.$name.'.'.$file->getClientOriginalExtension(); 
            if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)){
                return '--error--This file type ('.$file->getClientOriginalExtension().') is not supported.';
            }

            $destination = base_path().'/assets/uploads/';
            $upload_success = Input::file('file')->move($destination, $filename);
            if( $upload_success ) {
                // delete old file
                $answer = Block_answer::where('user_id',Auth::user()->id)->where('block_id', Input::get('block'))->first();
                if($answer==null){
                    $answer = new Block_answer();
                    $answer->user_id = Auth::user()->id;
                    $answer->block_id = Input::get('block');                    
                }
                else{
                    $old = $answer->answer;
                    @unlink(base_path().'/assets/uploads/'.$old);
                }
                
                $answer->answer = $filename;
                $answer->save();
                $response['url'] =  url('assets/uploads/'.$filename);
                $response['size'] =  human_filesize(filesize("assets/uploads/$filename"));
                return json_encode($response);
            } 
            else {
               return '--error--An unexpected error was encountered.';
            }
        }
        catch(Exception $e){
             $file = Input::file('file');
             $file_size = intval(toByteSize($file->getSize().' B'));
             if ($file_size > $file_max_byte) return "--error--Your file size exceedes our current limit ($file_max $file_max_meassure_unit)!";
        }
            
    }
    
    public function attach(){
        $file_max = ini_get('upload_max_filesize');
        $file_max_str_leng = strlen($file_max);
        $file_max_meassure_unit = substr($file_max,$file_max_str_leng - 1,1);
        $file_max_meassure_unit = $file_max_meassure_unit == 'K' ? 'kb' : ($file_max_meassure_unit == 'M' ? 'mb' : ($file_max_meassure_unit == 'G' ? 'gb' : 'units'));
        $file_max = substr($file_max,0,$file_max_str_leng - 1);
        $file_max_byte = intval(toByteSize($file_max.' '.$file_max_meassure_unit));
        
        
        try{
            $file = Input::file('file');
            $file_size = intval(toByteSize($file->getSize().' B'));
            if ($file_size==0 || $file_size > $file_max_byte) {
                $response['status'] = 'error';
                $response['text'] = 'Your file size exceedes our current limit ('.$file_max.' '.$file_max_meassure_unit.').';
                return json_encode($response);
            }
            $allowed = array('jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'pdf', 'odt', 'zip');
            $installation = sys_settings('installation');
            $name = Auth::user()->id.Str::random(12).rand(111,999);

            $filename = $installation.'-'.$name.'.'.$file->getClientOriginalExtension(); 
            if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)){
                $response['status'] = 'error';
                $response['text'] = 'This file type ('.$file->getClientOriginalExtension().') is not supported.';
                return json_encode($response);
            }

            $destination = base_path().'/assets/uploads/attachments';
            $upload_success = Input::file('file')->move($destination, $filename);
            
            if( $upload_success ) {
                    
                $attch = new Attachment();
                $attch->filename = $filename;
                $attch->orig_name = $file->getClientOriginalName();
                if(!$attch->save()){
                    $response['status'] = 'error';
                    $response['text'] = format_validation_errors( $attch->errors()->all());
                    return json_encode($response);
                }
                
                $response['url'] =  url('assets/uploads/attachments/'.$filename);
                $response['orig_name'] = $attch->orig_name;
                $response['id'] = $attch->id;
                $response['size'] = human_filesize(filesize("assets/uploads/attachments/$filename"));
                $response['status'] = 'success';
                return json_encode($response);
            } 
            else {
                $response['status'] = 'error';
                $response['text'] = 'An unexpected error was encountered.';
                return json_encode($response);
            }
        }
        catch(Exception $e){
            $response['status'] = 'error';
            $response['text'] = 'An unexpected error was encountered.';
            return json_encode($response);
        }
            
    }
    
    public function delete_attachment(){
        $att = Attachment::find(Input::get('id'));
        if($att!=null && $att->remark_id==0 && $att->comment_id==0 && $att->pm_id==0 && $att->conversation_id==0){
            @unlink(base_path().'/assets/uploads/attachments/'.$att->filename);
            $att->delete();
        }
    }
    
    public function remark_reply(){
        $admin = Conversation::where('user_id', Auth::user()->id)->where('lesson_id', Input::get('lesson'))->first();
        if($admin!=null) $admin = $admin->admin_id;
        else{
            $lessons = Lesson::where('program_id', Session::get('program_id'))->lists('id');
            $admin = Conversation::where('user_id', Auth::user()->id)->whereIn('lesson_id', $lessons)->first()->admin_id;
        }
       
        $remark = new Conversation();
        $remark->content = Input::get('reply_txt');
        $remark->user_id = Auth::user()->id;
        $remark->lesson_id = Input::get('lesson');
        $remark->admin_id = $admin;
        $remark->posted_by = 'user';
        $remark->attended = 0;
        $remark->read = 1;
        $remark->save();
        
        $ids = json_decode(Input::get('attachments'));
        if(count($ids)>0){
            Attachment::whereIn('id', $ids)->update(array('conversation_id'=>$remark->id));
        }
        
        
        $response['html'] = View::make('pages.lesson.remarks')->with('remarks',array($remark))->render();
        $response['id'] = $remark->id;
        return json_encode($response);
    }
    
    public function conversation($lesson_id, $user_id=0){
        $lesson = Lesson::find($lesson_id);
        $lessons = Lesson::where('program_id',Session::get('program_id'))->lists('id');
        if(!admin()) $user_id = Auth::user()->id;
        else Session::flash('user_id',$user_id);
        $remarks = Conversation::where('user_id', $user_id)->whereIn('lesson_id',$lessons)->orderBy('id','ASC')->get();
        $meta['pageTitle'] = 'Conversation';
        return View::make('pages.lesson.conversation')->withRemarks($remarks)->withLesson($lesson)->withMeta($meta);
    }

}
