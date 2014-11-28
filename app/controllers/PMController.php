<?php

class PMController extends BaseController {

    public function __construct(){
        $this->beforeFilter('auth');
        $this->beforeFilter('program', array('except' =>'new_pm'));
    }
    
    public function index($search=''){
        if($search=='filter:unread'){
            Input::merge(array('filter'=>'unread'));
            $search = '';
        }
        $meta['header_img_text'] = 'Private Messages';
        $recipients = $this->recipients();
        if(admin()){
            if(!Input::has('filter') || Input::get('filter')=='pm'){
                Input::merge(array('filter' => 'pm'));
                $convo = Conversation::whereRaw('
                    ((`is_pm` = 1 AND `user_id` = '.Auth::user()->id.' AND `posted_by` = "admin")
                    OR (`is_pm` = 1 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user"))' )
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
            elseif(Input::get('filter')=='question'){
                $convo = Conversation::whereRaw('
                    ( (`is_pm` = 0 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `block_answer_id` > 0))' )
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
            elseif(Input::get('filter')=='lesson'){
                $convo = Conversation::whereRaw('
                    ( (`is_pm` = 0 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `lesson_id` > 0))' )
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
            else{
                $convo = Conversation::whereRaw('
                    ( (`is_pm` = 0 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user")
                    OR (`is_pm` = 1 AND `user_id` = '.Auth::user()->id.' AND `posted_by` = "admin")
                    OR (`is_pm` = 1 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user"))' )
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
        }
        else{
            
            if(Input::get('filter')=='pm'){
                $convo = Conversation::whereRaw('`is_pm` = 1 AND `posted_by` = "admin" AND `user_id` = '.Auth::user()->id)
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
            elseif(Input::get('filter')=='question'){
                $convo = Conversation::whereRaw('`is_pm` = 0 AND `block_answer_id` > 0 AND `posted_by` = "admin" AND `user_id` = '.Auth::user()->id)
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
            elseif(Input::get('filter')=='lesson'){
                $convo = Conversation::whereRaw('`is_pm` = 0 AND `lesson_id` > 0 AND `posted_by` = "admin" AND `user_id` = '.Auth::user()->id)
                        ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate(10);
            }
            elseif(Input::get('filter')=='unread'){
                 $convo = Conversation::whereRaw('`posted_by` = "admin" AND `user_id` = '.Auth::user()->id)
                     ->where('program_id', Session::get('program_id'))->where('read',0)->orderBy('id','desc')->paginate(10);
            }
            else{
                $convo = Conversation::whereRaw('`posted_by` = "admin" AND `user_id` = '.Auth::user()->id)->where('program_id', Session::get('program_id'))
                    ->orderBy('id','desc')->paginate(10);
            }            
        }
        if(Request::ajax()){
            return View::make('pm.conversations')->withConvo($convo)->render();
        }
        $admin = User::first();     
        return View::make('pm.index')->withMeta($meta)->withRecipients($recipients)->withConvo($convo)->withSearch($search)->withAdmin($admin);
    }
    
    public function search(){
        if(trim(Input::get('term'))=='') return;
        if(strpos(Input::get('term'),'pmid--')!==false){
            $id = str_replace('pmid--', '', Input::get('term'));
            $sql = "`id` = '$id'";
        }
        else $sql = '`content` LIKE "%'.Input::get('term').'%"';
        
        if(admin()){
            $convo = Conversation::whereRaw($sql.' AND
                ( (`is_pm` = 0 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user")
                OR (`is_pm` = 1 AND `user_id` = '.Auth::user()->id.' AND `posted_by` = "admin")
                OR (`is_pm` = 1 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user"))' )
                    ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->get();
        }
        else{
            $convo = Conversation::whereRaw($sql.' AND `posted_by` = "admin" AND `user_id` = '.Auth::user()->id)->where('program_id', Session::get('program_id'))
                    ->orderBy('id','desc')->get();
        }
        
        return View::make('pm.conversations')->withConvo($convo)->render();
    }
    
    public function store(){
        if(!Input::has('to')){
            $response['status'] = 'danger';
            $response['text'] = 'No recipient specified';
        }
        else{
            $c = new Conversation();
            if(admin()){
                $c->admin_id = Auth::user()->id;
                $c->user_id = Input::get('to');
                $c->attended = 1;
                $c->posted_by = 'admin';
            }
            else{
                $c->user_id = Auth::user()->id;
                $c->admin_id = Input::get('to');
                $c->read = 1;
                $c->posted_by = 'user';
            }
            $c->content = Input::get('message');
            $c->is_pm = 1;
            if($c->save()){
                $response['status'] = 'success';
                $response['text'] = 'Message Sent';
                $response['callback'] = 'pm_callback';
            }
            else{
                $response['status'] = 'danger';
                $response['text'] = 'Something bad happened...';
                $response['error'] = format_validation_errors($c->errors()->all());
            }
        }
        
        return json_encode($response);
    }
    
    public function recipients(){
        if(admin()){
            //$ids = DB::table('assigned_roles')->where('role_id',2)->lists('user_id');
            $ids = DB::table('programs_users')->where('program_id',Session::get('program_id'))->lists('user_id');
        }
        else $ids = DB::table('assigned_roles')->where('role_id',1)->lists('user_id');
        if(count($ids)==0) $ids = array(0);
        $recipients = User::whereIn('id', $ids)->orderBy('username','ASC')->get();
        return $recipients;
    }
    
    public function load_convo($id){
        $convo = Conversation::find($id);
        $skip = Input::has('skip') ? Input::get('skip') : 0;
        //if($skip>0) $skip--;
        //$take = Input::has('skip') ? 2 : 1;
        $take = 2;
        if($convo->is_pm==0){
            if($convo->lesson_id>0){
                $remarks = Conversation::where('user_id', $convo->user_id)->where('lesson_id',$convo->lesson_id)
                        ->where('id','<=', $id)->orderBy('id','DESC')->take($take)->skip($skip)->get();
                $url = url('go_to/'.$convo->id);
                $field = admin() ? 'attended' : 'read';
                foreach($remarks as $c){
                    $c->timestamps = false;
                    $c->$field = 1;
                    $c->save();
                }
                if(admin()) $url.="/$convo->user_id";
                if(!Input::has('skip')) {
                    $btn = "
                        <center><a href='".url($url)."'>Go to lesson</a></center>
                        <button type='button' class='btn btn-default earlier-convo' data-url='".url('load_convo/'.$id)."'
                        data-convo='$id' data-skip='2'>Load Earlier Messages</button><br />";
                    return $btn.View::make('pages.lesson.remarks')->withRemarks($remarks)->withReverse(1)->withIs_inbox(1)->render();
                }
                else return View::make('pages.lesson.remarks')->withRemarks($remarks)->withReverse(1)->withIs_inbox(1)->render();
            }
            else{
                $answer = Block_answer::find($convo->block_answer_id);
                $block = Block::find($answer->block_id);
                $lesson = Lesson::find($block->lesson_id);
                $url = url('go_to/'.$id);
                if(admin()) $url.="/$convo->user_id";
                else $url.='/0';
                $url.="/comments-$convo->block_answer_id";
                
                $comments = Conversation::where('block_answer_id', $convo->block_answer_id)
                        ->where('id','<=', $id)->orderBy('id','DESC')->take($take)->skip($skip)->get();
                $field = admin() ? 'attended' : 'read';
                foreach($comments as $c){
                    $c->timestamps = false;
                    $c->$field = 1;
                    $c->save();
                }
                if(!Input::has('skip')){
                    $btn = "
                        <center><a href='".url($url)."'>Go to question</a></center>
                        <button type='button' class='btn btn-default earlier-convo' data-url='".url('load_convo/'.$id)."'
                        data-convo='$id' data-skip='2'>Load Earlier Messages</button><br />";
                    return $btn.View::make('pages.lesson.comments')->withComments($comments)->render();
                }
                else return View::make('pages.lesson.comments')->withComments($comments)->render();
            }
        }
        else{
            $comments = Conversation::where('is_pm', 1)->where('id','<=', $id)
                    ->whereRaw("((`user_id` = $convo->user_id AND `admin_id` = $convo->admin_id) 
                        OR (`user_id` = $convo->admin_id AND `admin_id` = $convo->user_id))")
                    ->orderBy('id','DESC')->take($take)->skip($skip)->get();
            
            foreach($comments as $c){
                if(!admin()) $field = 'read';
                else{
                    if($c->posted_by=='admin'){
                         if($c->user_id == Auth::user()->id ) $field = 'read';
                         else $field = 'attended';
                    }
                    else{
                        $field = 'attended';
                    }
                }
                $c->$field = 1;
                $c->save();
            }
            if(!Input::has('skip')){
                $btn = "
                    <button type='button' class='btn btn-default earlier-convo' data-url='".url('load_convo/'.$id)."'
                    data-convo='$id' data-skip='2'>Load Earlier Messages</button><br />";
                return $btn.View::make('pages.lesson.comments')->withComments($comments)->render();
            }
            else return View::make('pages.lesson.comments')->withComments($comments)->render();
        }
    }
    
    public static function inbox_count(){
        if(admin()){
            //
            return Conversation::whereRaw('
                (`is_pm` = 0 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `attended` = 0) 
                OR (  (`is_pm` = 1 AND `user_id` = '.Auth::user()->id.' AND `posted_by` = "admin" AND `read` = 0)
                OR (`is_pm` = 1 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `attended` = 0))' )
                    ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->count();
        }
        else{
            return Conversation::whereRaw('`posted_by` = "admin" AND `user_id` = '.Auth::user()->id)->where('program_id', Session::get('program_id'))
                    ->where('read',0)->orderBy('id','desc')->count();
        }
    }
    
    public static function new_pm(){
        if(admin()){
            //
            return Conversation::whereRaw('
                (  (`is_pm` = 1 AND `user_id` = '.Auth::user()->id.' AND `posted_by` = "admin" AND `read` = 0)
                OR (`is_pm` = 1 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `attended` = 0))' )
                    ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->count();
        }
        else{
            return Conversation::whereRaw('`posted_by` = "admin" AND `user_id` = '.Auth::user()->id)->where('program_id', Session::get('program_id'))
                    ->where('read',0)->orderBy('id','desc')->count();
        }
    }
    
    public static function unread_pm($limit=5){
        //(`is_pm` = 0 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `attended` = 0) OR 
        return Conversation::whereRaw('
                ( 
               (`is_pm` = 1 AND `user_id` = '.Auth::user()->id.' AND `posted_by` = "admin" AND `read` = 0)
                OR (`is_pm` = 1 AND `admin_id` = '.Auth::user()->id.' AND `posted_by` = "user" AND `attended` = 0))' )
                    ->where('program_id', Session::get('program_id'))->orderBy('id','desc')->paginate($limit);
    }
    
    public function go_to($id, $segment=0, $hash=''){
        $convo = Conversation::where('id', $id)->whereRaw('`user_id`='.Auth::user()->id.' OR `admin_id`='.Auth::user()->id)->first();
        if($convo!=null){
            if($convo->lesson_id>0){
                $url = 'lesson/'.$convo->lesson->slug;
                Session::set('program_id',$convo->lesson->program_id);
            }
            else{
                $answer = Block_answer::find($convo->block_answer_id);
                $block = Block::find($answer->block_id);
                $lesson = Lesson::find($block->lesson_id);
                $url = 'lesson/'.$lesson->slug;
                Session::set('program_id',$lesson->program_id);
            }
            
            if($segment!=0) $url.='/'.$segment;
            if($hash!='') $url .='#'.$hash;
            return Redirect::to($url);
        }
    }
    
    

}
