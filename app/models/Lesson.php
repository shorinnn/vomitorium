<?php
use LaravelBook\Ardent\Ardent;

class Lesson extends Ardent {
	public static $rules = array(
            'title'=>'required',
            'ord' => 'required',
            'slug' =>'required|unique:lessons'
        );
        
        public static $relationsData = array(
        'program' => array(self::BELONGS_TO, 'Program'),
        'chapter' => array(self::BELONGS_TO, 'Chapter'),
        'blocks' => array(self::HAS_MANY, 'Block'),
        'remark' => array(self::HAS_MANY, 'Remark'),
        'alerts' => array(self::HAS_MANY, 'Lesson_alert')
      );
        
    
    public function move($direction='up'){
        // can't move last item further down
        if($this->ord == DB::table('lessons')->max('ord') && $direction=='down') return;
        // can't move first item further up
        if($this->ord==1 && $direction=='up') return;
        // swap positions 
        if($direction=='up'){
            $other = Lesson::where('chapter_id', $this->chapter_id)->where('ord', $this->ord - 1)->first();
            $other->ord++;
            $this->ord--;
        }
        else{
            $other = Lesson::where('chapter_id', $this->chapter_id)->where('ord', $this->ord + 1)->first();
            $other->ord--;
            $this->ord++;
        }
        
        if(!$other->updateUniques()) {
            return format_validation_errors($other->errors()->all());
        }
        if(!$this->updateUniques()){
            return format_validation_errors($other->errors()->all());   
        }
    }
    
    public function update_field($field, $value){
        if($field=='slug') $this->$field = Str::slug($value);
        elseif($field=='chapter_id'){
            $value = str_replace('_', '', $value);
            // update the order for remaining chapters
            DB::table('lessons')->where('chapter_id', $this->chapter_id)->where('ord','>', $this->ord)->decrement('ord');
            if($value>1) {
                $chapter = Chapter::find($value);
                $this->chapter_ord = $chapter->ord;
            }
            else $this->chapter_ord = 0;
            if($value!=0) $this->ord = DB::table('lessons')->where('chapter_id', $value)->max('ord') + 1;
            $this->$field = $value;
        }
        elseif($field=='title'){
            if($this->slug == Str::slug($this->title)){
                $this->slug = Str::slug($value);
            }
            $this->title = $value;
        }
        else $this->$field = $value;
         if($this->updateUniques()){
             if($field=='chapter_id'){
                 $response['text'] = 'chapter_id';
                 $response['pk'] = Input::get('pk');
                 $response['edit_url'] = url("lessons/$this->id/editor");
                 return Response::make(json_encode($response), 200);
             }
             elseif($field=='title'){
                 $response['text'] = 'title';
                 $response['slug'] = $this->slug;
                 $response['url'] = url("lesson/$this->slug");
                 return Response::make(json_encode($response), 200);
             }
             elseif($field=='slug'){
                 $response['text'] = 'permalink';
                 $response['url'] = url("lesson/$this->slug");
                 return Response::make(json_encode($response), 200);
             }
             else{}
            return Response::make('success', 200);
        }
        else{
            return Response::make(format_validation_errors($this->errors()->all()), 400);
        }
    }
    
    public function create_chapter($params){
        if(isset($params['chapter'])) $params['chapter'] = str_replace('_', '', $params['chapter']);
        $chapter = new Chapter();
        $chapter->title = $params['title'];
        $chapter->ord = 0;
        $chapter->program_id = Session::get('program_id');
        
        if(!$chapter->save()){
            return json_encode(array('status'=>'danger', 'text' => format_validation_errors($chapter->errors()->all())));
        }
        $this->chapter_id = $chapter->id;
        $this->updateUniques();
        // if this is the only chapter, set ord = 1
        if(DB::table('chapters')->where('program_id',Session::get('program_id'))->count()==1){
            $chapter->ord = 1;
        }
        else{
            if($params['chapter']==0){
                $other_chapter = Chapter::where('program_id',Session::get('program_id'))->orderBy('ord','DESC')->first();
            }
            else $other_chapter = Chapter::find($params['chapter']);
            
            if($params['order']=='after' || $params['order']=='at_the_end'){
                DB::table('chapters')->where('program_id',Session::get('program_id'))->where('ord','>', $other_chapter->ord)->increment('ord');
                $chapter->ord = $other_chapter->ord + 1;
            }
            else{
                $chapter->ord = $other_chapter->ord;
                DB::table('chapters')->where('program_id',Session::get('program_id'))->where('ord','>=', $other_chapter->ord)->increment('ord');
                
            }
        }
        $chapter->updateUniques();
        return json_encode(array('status'=>'success', 'text' => 'Chapter Created', 'id' => "_$chapter->id",'title' => $chapter->title));
    }
    
      public function beforeDelete() {
         // delete associated blocks
          if($this->blocks()->count() > 0){
             foreach($this->blocks()->get() as $b){
                 $b->delete();
             }
          }
         // delete associated remarks         
         if($this->remark()->count() > 0){
             foreach($this->remark()->get() as $r){
                 $r->delete();
             }
         }
         // update the order for remaining chapters
         DB::table('lessons')->where('program_id',Session::get('program_id'))->where('chapter_id', $this->chapter_id)->where('ord','>', $this->ord)->decrement('ord');               
      }
      
      public function already_submitted(){
          if(Auth::guest()) return false;
          if($this->progress()>0)  return true;
          return false;
      }
     
      public function all_answered(){
          if(Auth::guest()) return false;
          if($this->progress()==100) return true;
          return false;
//          foreach($this->blocks as $b){
//              if($b->type=='text') continue;
//              if($b->type=='dynamic') continue;
//              if($b->type=='top_skills') continue;
//              if($b->type=='answer'){
//                  $parent = Block::find($b->answer_id);
//                  if($parent!=null){
//                      if($parent->answer_type=='Open Ended' || $parent->answer_type=='Multiple Choice') continue;
//                  }
//              }
//              $user_id = (admin() && Session::has('user_id')) ? Session::get('user_id') : Auth::user()->id;
//              $c = DB::table('block_answers')->where('block_id', $b->id)->where('user_id',$user_id)->count();
//              //if($c==0) return false;
//              if($c>0) return true;
//          }
//          //return true;
//          return false;
      }
      
      public static function user_courses($all_courses = false){
          if(Auth::guest()) return null;
           $user = User::find(Auth::user()->id);
          if($all_courses==true){
              $lessons = Lesson::where('program_id', Session::get('program_id'))->where('published',1)->orderBy('chapter_ord','ASC')->orderBy('ord','ASC')->get();
              return $lessons;
          }
          if($user->last_lesson > 0 ){
              $last_lesson = Lesson::find($user->last_lesson);
              $lessons = Lesson::where('program_id', Session::get('program_id'))->where('published',1)->orderBy('chapter_ord','ASC')->orderBy('ord','ASC')->get();
              $ret = new Illuminate\Database\Eloquent\Collection;
              foreach($lessons as $l){
                  if($l->chapter_ord < $last_lesson->chapter_ord) $ret->add($l);
                  if(($l->chapter_ord == $last_lesson->chapter_ord ) && ($l->ord <= $last_lesson->ord)) $ret->add($l);
              }
              return $ret;
          }
          // get lessons where the user has submitted answers
          $answers = Block_answer::where('user_id',Auth::user()->id)->lists('block_id');
          if($answers!=null){
              $blocks = Block::whereIn('id', $answers)->lists('lesson_id');
              if($blocks!=null){
                  return $lessons = Lesson::where('program_id', Session::get('program_id'))->where('published',1)->whereIn('id',$blocks)->orderBy('chapter_ord','ASC')->orderBy('ord','ASC')->get();
              }
          }
      }
      
      public static function first(){
          $l = Lesson::where('published',1)->where('program_id', Session::get('program_id'))->orderBy('chapter_ord','ASC')->orderBy('ord','ASC')->first();
          if($l==null) return '';
          return URL('lesson/'.$l->slug);
      }
      
      public static function last(){
          $last_lesson = Auth::user()->last_lesson;
          if($last_lesson=='0'){
              return null;
          }
          $last_lesson = json_decode($last_lesson, true);
          if(!isset($last_lesson[Session::get('program_id')])) return null;
          return Lesson::find($last_lesson[Session::get('program_id')]);
      }
      
      public static function save_user_progress($l){
           if(Auth::guest()) return null;
           // add the lesson to the visited list
           $user = User::find(Auth::user()->id);
           if($user->lessons==''){
               $lessons = array();
           }
           else{
               $lessons = json_decode($user->lessons, true);
           }
           $lessons[$l->id] = $l->id;
           $user->lessons = json_encode($lessons);
           $user->updateUniques();
           $last_lesson_arr = array();
           if($user->last_lesson!=='0'){
               $last_lesson_arr = json_decode( $user->last_lesson, true);
               if(isset($last_lesson_arr[Session::get('program_id')])) $user->last_lesson = $last_lesson_arr[Session::get('program_id')];
               else $user->last_lesson = '0';
           }
           if($user->last_lesson !== '0' ){
              $last_lesson = Lesson::find($user->last_lesson);
              if($last_lesson==null){
                  $last_lesson = new stdClass();
                  $last_lesson->ord = -1;
                  $last_lesson->chapter_ord = -1;
              }
              if(($l->chapter_ord > $last_lesson->chapter_ord)){
                  $user->last_lesson = $l->id;
              }
              if(($l->chapter_ord == $last_lesson->chapter_ord) && ($l->ord > $last_lesson->ord)){
                  $user->last_lesson = $l->id;
              }
              $last_lesson_arr[Session::get('program_id')] = $user->last_lesson;
              $user->last_lesson = json_encode($last_lesson_arr);
           }
           else{
               $last_lesson_arr[Session::get('program_id')] =  $l->id;
                $user->last_lesson = json_encode($last_lesson_arr);
              // $user->last_lesson = json_encode( array(Session::get('program_id') => $l->id));
               
           }
          $user->updateUniques();
          $data['start_date'] = date("Y-m-d 00:00:00");
          DB::table('programs_users')->where('user_id', Auth::user()->id)->where('program_id', Session::get('program_id'))->whereNull('start_date')->update($data);
      }
      
      public static function move_block_to_pos($move, $target){
          if($move==$target) return;
            if($target==0){
                $move = Block::find($move);
                if($move==null) return;
                if($move->ord==1) return;
                DB::table('blocks')->where('lesson_id', $move->lesson_id)->where('ord','>', $move->ord)->decrement('ord');
                DB::table('blocks')->where('lesson_id', $move->lesson_id)->increment('ord');
                $move->ord = 1;
            }
            else{
                $move = Block::find($move);
                $target = Block::find($target);
                if($move==null || $target==null) return;
                if($move->lesson_id != $target->lesson_id) return;
                $target_ord = $target->ord;
                $move_ord = $move->ord;
                DB::table('blocks')->where('lesson_id', $move->lesson_id)->where('ord','>', $move->ord)->decrement('ord');
                DB::table('blocks')->where('lesson_id', $move->lesson_id)->where('ord','>', $target->ord)->increment('ord');
                $move = Block::find($move->id);
                $target = Block::find($target->id);
                $move->ord = $target->ord + 1;
                if($move_ord < $target_ord){
                    DB::table('blocks')->where('lesson_id', $move->lesson_id)->where('ord','>=', $move->ord)->increment('ord');
                }
            }
            $move->save();
          return 'moved';
      }
      
      public function progress(){
          if(Auth::guest()) return 0;
          if(admin() && Session::has('user_id')) $user_id = Session::get('user_id');
          else $user_id = Auth::user()->id;
          // get the total answerable blocks count
          $types = array('image_upload', 'file_upload', 'sortable', 'question');
          $blocks = Block::where('lesson_id', $this->id)->whereIn('type', $types)->get();
          $total = Block::where('lesson_id', $this->id)->whereIn('type', $types)->count();
          if($this->id==52) {
              foreach($blocks->lists('id') as $key=>$val){
                  //echo "$val, ";
              }
          }
          $ids = $blocks->lists('id');
          if(count($ids)==0) return 100;
          $answered = Block_answer::whereIn('block_id', $ids)->where('user_id', $user_id)->whereNotNull('answer')->count();
                  //->whereRaw('LENGTH(answer) > 0')->count();
          return ceil($answered * 100 / $total);
          //return $answered->count();
      }
      
      public function current_answer_count(){
          if(Auth::guest()) return 0;
          if(admin() && Session::has('user_id')) $user_id = Session::get('user_id');
          else $user_id = Auth::user()->id;
          // get the total answerable blocks count
          $types = array('image_upload', 'sortable', 'question');
          $blocks = Block::where('lesson_id', $this->id)->whereIn('type', $types)->get();
          $ids = $blocks->lists('id');
         return  Block_answer::whereIn('block_id', $ids)->where('user_id', $user_id)->whereNotNull('answer')->whereRaw('LENGTH(answer) > 0')->count();
      }
      
      public function program_progress(){
          if(Auth::guest()) return 0;
          if(admin() && Session::has('user_id')) $user_id = Session::get('user_id');
          else $user_id = Auth::user()->id;
          $lessons = Lesson::where('program_id', Session::get('program_id'))->where('published',1)->lists('id');
          $total = count($lessons);
          $visited = json_decode(User::find($user_id)->lessons, true);
          if($visited==null) $visited = array();
          $current = 0;
          foreach($visited as $v){
              if(in_array($v, $lessons)){
                  $l = Lesson::find($v);
                  if($l->progress()> 0) $current++;
              }
          }
          return ceil($current * 100 / $total);
      }
      
      public static function move_after($move, $target){
          if($move==$target) return;
            if($target==0){
                $move = Lesson::find($move);
                if($move==null) return;
                if($move->ord==1) return;
                DB::table('lessons')->where('chapter_id', $move->chapter_id)->where('ord','>', $move->ord)->decrement('ord');
                DB::table('lessons')->where('chapter_id', $move->chapter_id)->increment('ord');
                $move->ord = 1;
            }
            else{
                $move = Lesson::find($move);
                $target = Lesson::find($target);
                if($move==null || $target==null) return;
                if($move->chapter_id != $target->chapter_id) return;
                if($move->ord == $target->ord+1) return;
                $target_ord = $target->ord;
                $move_ord = $move->ord;
                DB::table('lessons')->where('chapter_id', $move->chapter_id)->where('ord','>', $move->ord)->decrement('ord');
//                DB::table('lessons')->where('chapter_id', $move->chapter_id)->where('ord','>', $target->ord)->increment('ord');
                $move = Lesson::find($move->id);
                $target = Lesson::find($target->id);
                $move->ord = $target->ord + 1;
                //if($move_ord < $target_ord){
                   DB::table('lessons')->where('chapter_id', $move->chapter_id)->where('ord','>=', $move->ord)->increment('ord');
                //}
            }
            $move->updateUniques();
      }
      
}
