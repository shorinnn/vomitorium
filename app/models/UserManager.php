<?php
use LaravelBook\Ardent\Ardent;

class UserManager extends Ardent {
	
    public static function update_field($params){
        $user = User::find($params['pk']);
        if($params['name']=='role'){
             $user->roles()->sync(array($params['value']));
        }
        if($params['name']=='programs'){
            DB::table('programs_users')->where('user_id',$params['pk'])->whereNotIn('program_id', $params['value'])->delete();
            foreach($params['value'] as $p){
                if(DB::table('programs_users')->where('user_id',$params['pk'])->where('program_id',$p)->count()==0){
                    $data['user_id'] = $params['pk'];
                    $data['program_id'] = $p;
                    DB::table('programs_users')->insert($data);
                }
            }
        }
        elseif($params['name']=='confirmed'){
            DB::table('users')->where('id', $params['pk'])->update(array($params['name'] => $params['value']));
        }
        else{
            $user->$params['name'] = $params['value'];
            if(!$user->updateUniques()){            
                return Response::make(format_validation_errors($user->errors()->all()), 400);
            }
        }
        
        return Response::make('success', 200);
    }
    
    public static function destroy($id=0){
        $user = User::find($id);
        if($user->hasRole('Admin')){
            $response['status'] = 'danger';
            $response['text'] = 'Cannot delete Admin user.';
        }
        elseif($user->delete()){
            DB::table('assigned_roles')->where('user_id', $id)->delete();
            $response['status'] = 'success';
            $response['text'] = 'User has been deleted.';
        }
        else{
            $response['status'] = 'danger';
            $response['text'] = 'Cannot delete - an error occurred.';
        }
        
        return json_encode($response);
    }
    
    public static function change_password($params){
        $password = Hash::make($params['password']);
        if(strlen($params['password'])>=4 &&  DB::table('users')
            ->where('id',$params['id'])->update(array('password'=>$password)) )
        {
            $response['status'] = 'success';
            $response['text'] = "Password changed";
        }
        else{
            $response['status'] = 'danger';
            $response['text'] = "An error occurred";
           }
        return json_encode($response);
    }
    
    public static function new_remarks($id){
        $sql = "`lesson_id` IN (SELECT `id` FROM `lessons` WHERE `program_id` = '".Session::get('program_id')."')";
        return Conversation::where('user_id', $id)->whereRaw($sql)->where('read',0)->where('posted_by','admin')->get();
        //return Remark::where('user_id', $id)->where('read',0)->get();
    }
    
    public static function new_comments($id){
        $sql = " `block_id` IN (SELECT `id` FROM `blocks` WHERE `lesson_id` IN 
            (SELECT `id` FROM `lessons` WHERE `program_id` = '".Session::get('program_id')."'))";
        $blocks = Block_answer::whereRaw($sql)->where('user_id',$id)->lists('id');
        if(count($blocks)==0) $blocks = array(-10);
        return Conversation::where('read',0)->where('is_pm',0)->whereIn('block_answer_id',$blocks)->where('posted_by','admin')
                ->orderBy('id','desc')->get();
    }
    
//    public static function new_admin_comments($limit = 15){
//        return Answer_comment::where('admin_id',0)->orderBy('updated_at','desc')->with('block_answer.block.lesson')->with('user')->paginate($limit);
//    }
    
//    public static function unattented_comments($limit=15){
//        return Answer_comment::where('admin_id',0)->where('attended',0)->orderBy('updated_at','desc')->with('block_answer.block.lesson')->with('user')->paginate($limit);
//    }
    
    public static function new_submissions($limit = 5){
        $program_ids = Program::find(Session::get('program_id'))->users()->lists('user_id');
        if(count($program_ids)==0) $program_ids = array(0);
        $res  = Block_answer::whereIn('user_id', $program_ids)->where('attended',0)
                ->whereRaw("`block_id` IN (SELECT `id` FROM `blocks` WHERE `lesson_id` IN (
                    SELECT `id` FROM `lessons` WHERE `program_id` = '".Session::get('program_id')."' ))")
                ->orderBy('updated_at','desc')->with('block.lesson')->with('user')->paginate($limit);
        return $res;
    }
    
//    public static function unattended_submissions($limit = 15){
//        return Block_answer::where('attended',0)->orderBy('updated_at','desc')->with('block.lesson')->with('user')->paginate($limit);
//    }
    
    public static function unattended_users($limit=5){
        $assigned = DB::table('assigned_clients')->where('program_id',Session::get('program_id'))->where('admin_id', Auth::user()->id)->lists('user_id');
        if(count($assigned)==0) $assigned = array('-1');
        $assigned = implode(',', $assigned);
        $non_assigned = DB::table('users')->whereRaw('`id` NOT IN (SELECT DISTINCT `user_id` FROM 
            `assigned_clients` WHERE `program_id` = "'.Session::get('program_id').'" )')->lists('id');
        if(count($non_assigned)==0) $non_assigned = array('-1');
        $non_assigned = implode(',', $non_assigned);
        
        $sql = " (`id` IN ($assigned) OR `id` IN ($non_assigned))
            AND (
       `id` IN (
           SELECT `user_id` FROM `remarks` WHERE `attended` = 0 AND `admin_reply` = 0 AND `admin_id` = '".Auth::user()->id."'
               AND `lesson_id` IN (SELECT `id` FROM `lessons` WHERE `program_id` = '".Session::get('program_id')."')
       )     
    OR `id` IN 
                    (SELECT `user_id` FROM `block_answers` WHERE `attended` = '0' AND `block_id` IN 
                        (SELECT `id` FROM `blocks` WHERE `lesson_id` IN 
                            (SELECT `id` FROM `lessons` WHERE `published` = 1 AND `program_id` = '".Session::get('program_id')."')))
                OR `id` IN 
                    (SELECT `user_id` FROM `conversations`  WHERE `posted_by` = 'user' AND `attended` = 0)) ";
        $program = Program::find(Session::get('program_id'));
        if($program==null) $program_ids = array();
        else $program_ids = $program->users()->lists('user_id');
        if(count($program_ids)==0) $program_ids = array('-1');
        
        $users =  User::whereRaw($sql)->whereIn('id',$program_ids)->get();//->paginate($limit);
        $collection = new Illuminate\Database\Eloquent\Collection;
        $arr = [];
        //$collection = new Illuminate\Pagination\Paginator;
        foreach($users as $u){
            if( !$u->chat_permission(Session::get('program_id'), 'coach_conversations') ) continue;
            $update = json_decode($u->last_update, true);
            $update = strtotime($update[Session::get('program_id')]);
            $u->last_program_update = $update;
            if(trim($update)=='') continue;
            do{
                $update++;
            }
            while(key_exists($update, $arr));
            $arr[$update] = $u;
        }
        ksort($arr);
        foreach($arr as $u){
            $collection->add($u);
        }
        $pagination = App::make('paginator');
        $count = $collection->count();
        $page = $pagination->getCurrentPage($count);
        $items = $collection->slice(($page - 1) * $limit, $limit)->all();
        $pagination = $pagination->make($items, $count, $limit);
        return $pagination;
    }
    
//    public static function get_unattended_users($limit=15){
//        $program_users = DB::table('programs_users')->where('program_id', Session::get('program_id'))->lists("user_id");
//        return User::whereHas('answers', function($q){
//                $q->where('attended', '=', 0);
//            })
//            ->orWhereHas('comments', function($q){
//                $q->where('attended', '=', 0);
//            })->orderBy('last_update','ASC')
//            ->whereIn('id',$program_users)
//            ->paginate($limit);
//    }
    
    public static function unattended_answers($user_id, $lesson=0){
        if($lesson==0){
            $sql = " `block_id` IN (SELECT `id` FROM `blocks` WHERE `lesson_id` IN 
                            (SELECT `id` FROM `lessons` WHERE `published` = 1 AND `program_id` = '".Session::get('program_id')."'))";
            return Block_answer::whereRaw($sql)->where('user_id',$user_id)->where('attended',0)->count();
           // return Block_answer::where('user_id',$user_id)->where('attended',0)->count();
        }
        $b = Block::where('lesson_id',$lesson)->lists('id');
        if(count($b)==0) $b = array('-1');
        return Block_answer::whereIn('block_id', $b)->where('user_id', $user_id)->where('attended',0)->count();
    }
    
     public static function unattended_remarks($user_id, $lesson=0){
        if($lesson==0){
            $lessons = Lesson::where('program_id', Session::get('program_id'))->lists('id');
            return Conversation::where('user_id',$user_id)->where('attended',0)->where('posted_by','user')->whereIn('lesson_id', $lessons)->count();
        }
        return Conversation::where('user_id',$user_id)->where('attended',0)->where('posted_by','user')->where('lesson_id', $lesson)->count();
    }
    
    public static function unattended_comments($user_id, $lesson=0){
        if($lesson==0){
             $sql = " `block_answer_id` IN (SELECT `id` FROM `block_answers` WHERE `block_id` IN 
                         (SELECT `id` FROM `blocks` WHERE `lesson_id` IN 
                            (SELECT `id` FROM `lessons` WHERE `published` = 1 AND `program_id` = '".Session::get('program_id')."')))";
            return Conversation::whereRaw($sql)->where('user_id',$user_id)->where('attended',0)->count();
            //return Answer_comment::where('user_id',$user_id)->where('attended',0)->count();
        }
        $b = Block::where('lesson_id',$lesson)->lists('id');
        if(count($b)==0) $b = array('-1');
        $b = Block_answer::whereIn('block_id',$b)->lists('id');
        if(count($b)==0) $b = array('-1');
        return Conversation::whereIn('block_answer_id', $b)->where('user_id', $user_id)->where('attended',0)->where('posted_by','user')->count();
    }
    
    public static function next_unattended($user_id, $lesson){
        $l = Lesson::find($lesson);
        $answer_lesson = DB::select(DB::raw("SELECT * FROM `lessons` WHERE `id` != '$lesson'
            AND `program_id` = '".Session::get('program_id')."'
            AND `ord` > '$l->ord'
            AND `chapter_id` = '$l->chapter_id' AND `id` IN 
            (SELECT `lesson_id` FROM `blocks` WHERE `id` IN (
                SELECT `block_id` FROM `block_answers` WHERE `attended` = 0 AND `user_id` = '$user_id'
            )) ORDER BY `ord` ASC"));
        
        if($answer_lesson==null){
            $answer_lesson = DB::select(DB::raw("SELECT * FROM `lessons` WHERE `id` != '$lesson' 
            AND `program_id` = '".Session::get('program_id')."'
            AND `chapter_id` != '$l->chapter_id'
            AND `id` IN 
            (SELECT `lesson_id` FROM `blocks` WHERE `id` IN (
                SELECT `block_id` FROM `block_answers` WHERE `attended` = 0 AND `user_id` = '$user_id'
            )) ORDER BY `id` ASC"));
        }
        
        if($answer_lesson==null){
            $answer_lesson = DB::select(DB::raw("SELECT * FROM `lessons` WHERE `id` != '$lesson'
            AND `program_id` = '".Session::get('program_id')."'
            AND `ord` < '$l->ord'
            AND `chapter_id` = '$l->chapter_id' AND `id` IN 
            (SELECT `lesson_id` FROM `blocks` WHERE `id` IN (
                SELECT `block_id` FROM `block_answers` WHERE `attended` = 0 AND `user_id` = '$user_id'
            )) ORDER BY `ord` ASC"));
        }
        
        $comment_lesson = DB::select(DB::raw("SELECT * FROM `lessons` WHERE `id` != '$lesson'
            AND `program_id` = '".Session::get('program_id')."'
            AND `ord` > '$l->ord'
            AND `chapter_id` = '$l->chapter_id'
            AND `id` IN 
            (SELECT `lesson_id` FROM `blocks` WHERE `id` IN (
                SELECT `block_id` FROM `block_answers` WHERE `id` IN 
                    (SELECT `block_answer_id` FROM `answer_comments` WHERE `attended` = 0 AND `user_id` = '$user_id')
            )) ORDER BY `id` ASC"));
        if($comment_lesson==null){
             $comment_lesson = DB::select(DB::raw("SELECT * FROM `lessons` WHERE `id` != '$lesson'
             AND `program_id` = '".Session::get('program_id')."'
            AND `chapter_id` != '$l->chapter_id'     
            AND `id` IN 
            (SELECT `lesson_id` FROM `blocks` WHERE `id` IN (
                SELECT `block_id` FROM `block_answers` WHERE `id` IN 
                    (SELECT `block_answer_id` FROM `answer_comments` WHERE `attended` = 0 AND `user_id` = '$user_id')
            )) ORDER BY  `id` ASC"));
        }
        
        if($comment_lesson==null){
            $comment_lesson = DB::select(DB::raw("SELECT * FROM `lessons` WHERE `id` != '$lesson'
            AND `program_id` = '".Session::get('program_id')."'
            AND `ord` < '$l->ord'
            AND `chapter_id` = '$l->chapter_id'
            AND `id` IN 
            (SELECT `lesson_id` FROM `blocks` WHERE `id` IN (
                SELECT `block_id` FROM `block_answers` WHERE `id` IN 
                    (SELECT `block_answer_id` FROM `answer_comments` WHERE `attended` = 0 AND `user_id` = '$user_id')
            )) ORDER BY `id` ASC"));
        }
        
        if($answer_lesson!=null && $comment_lesson!=null){
            if($comment_lesson[0]->chapter_ord > $answer_lesson[0]->chapter_ord){
                return $answer_lesson[0];
            }
            else if($comment_lesson[0]->chapter_ord < $answer_lesson[0]->chapter_ord){
                return $comment_lesson[0];
            }
            else{
                 if($comment_lesson[0]->ord > $answer_lesson[0]->ord) return $answer_lesson[0];
                 else return $comment_lesson[0];
            }
        }
        else if($comment_lesson!=null){
            return $comment_lesson[0];
        }
        elseif($answer_lesson!=null){
            return $answer_lesson[0];
        }
        else{
            return null;
        }
    }
    
    public static function compile_notifications($comments, $remarks){
        $notifications = array();
        if($comments->count() > 0){
            foreach($comments as $c){
                $key = strtotime($c->updated_at);
                if(!key_exists($key, $notifications)) $notifications[] = $c;
                else{
                    do{
                        $key +=1;
                    }
                    while(key_exists($key, $notifications));
                    $notifications[$key] = $c;
                }
            }
        }
        if($remarks->count() > 0){
            foreach($remarks as $c){
                $key = strtotime($c->updated_at);
                if(!key_exists($key, $notifications)) $notifications[] = $c;
                else{
                    do{
                        $key +=1;
                    }
                    while(key_exists($key, $notifications));
                    $notifications[$key] = $c;
                }
            }
        }
        
        krsort($notifications);
        return $notifications;
    }
    
}
