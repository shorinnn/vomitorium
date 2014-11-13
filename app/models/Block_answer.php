<?php
use LaravelBook\Ardent\Ardent;

class Block_answer extends Ardent {
    public static $rules = array(
            'user_id'=>'required|numeric',
            'block_id' => 'required|numeric'
        );
    
     public static $relationsData = array(
        'block' => array(self::BELONGS_TO, 'Block'),
        'answer_comments' => array(self::HAS_MANY,' Answer_comment'),
        'conversations' => array(self::HAS_MANY,' Conversation'),
        'user' => array(self::BELONGS_TO, 'User')
      );
     
     public static $unattended_items;
     
    
    public static function store_answers($params){
        $is_update = false;
        if(isset($params['score'])){
            foreach($params['score'] as $block => $value){
                $data = array();
                $data['score'] = $value;
                $data['independent_scores'] = $_POST['independent_score'][$block];
                if(self::where('user_id', Auth::user()->id)->where('block_id',$block)->count() > 0){
                    $answer = self::where('user_id', Auth::user()->id)->where('block_id',$block)->first();
                    $is_update = true;
                }
                else{
                    $answer = new Block_answer;
                }
                $data = json_encode($data);
                $answer->user_id = Auth::user()->id;
                $answer->block_id = $block;
                $answer->answer = $data;
                $answer->attended = 0;
                $answer->save();
            }
        }
        if(isset($params['open_answer'])){
            foreach($params['open_answer'] as $block => $value){
                if(self::where('user_id', Auth::user()->id)->where('block_id',$block)->count() > 0){
                    $answer = self::where('user_id', Auth::user()->id)->where('block_id',$block)->first();
                    $is_update = true;
                }
                else{
                    $answer = new Block_answer;
                }
                
                $answer->user_id = Auth::user()->id;
                $answer->block_id = $block;
                $answer->answer = $value;
                $answer->attended = 0;
                $answer->save();
            }
        }
        if(isset($params['mc-answer'])){
            foreach($params['mc-answer'] as $block => $value){
                if(self::where('user_id', Auth::user()->id)->where('block_id',$block)->count() > 0){
                    $answer = self::where('user_id', Auth::user()->id)->where('block_id',$block)->first();
                    $is_update = true;
                }
                else{
                    $answer = new Block_answer;
                }
                $answer->user_id = Auth::user()->id;
                $answer->block_id = $block;
                $answer->answer = json_encode($value);
                $answer->attended = 0;
                $answer->save();
            }
        }
        if(isset($params['sortable-answer'])){
            foreach($params['sortable-answer'] as $block => $value){
                if(trim($value)=='') continue;
                if(self::where('user_id', Auth::user()->id)->where('block_id',$block)->count() > 0){
                    $answer = self::where('user_id', Auth::user()->id)->where('block_id',$block)->first();
                    $is_update = true;
                }
                else{
                    $answer = new Block_answer;
                }
                $answer->user_id = Auth::user()->id;
                $answer->block_id = $block;
                $answer->answer = trim($value);
                $answer->attended = 0;
                $answer->save();
            }
        }
        if(isset($params['skill-answer'])){
            foreach($params['skill-answer'] as $block => $value){
                if(self::where('user_id', Auth::user()->id)->where('block_id',$block)->count() > 0){
                    $answer = self::where('user_id', Auth::user()->id)->where('block_id',$block)->first();
                    $is_update = true;
                }
                else{
                    $answer = new Block_answer;
                }
                $answer->user_id = Auth::user()->id;
                $answer->block_id = $block;
                $answer->answer = json_encode($value);
                $answer->attended = 0;
                $answer->save();
            }
        }
        if(isset($params['followup'])){
            foreach($params['followup'] as $block => $value){
                if(self::where('user_id', Auth::user()->id)->where('block_id',$block)->count() > 0){
                    $answer = self::where('user_id', Auth::user()->id)->where('block_id',$block)->first();
                    $is_update = true;
                }
                else{
                    $answer = new Block_answer;
                }
                $answer->user_id = Auth::user()->id;
                $answer->block_id = $block;
                $answer->answer = json_encode($value);
                $answer->attended = 0;
                $answer->save();
            }
        }
        if($is_update) Session::flash('success', "Answers updated! Coach has been alerted of the change");
     }
      
      public function beforeDelete(){
          DB::table('answer_comments')->where('block_answer_id',$this->id)->delete();
      }
      
      public function beforeSave(){
          self::$unattended_items = UserManager::unattended_answers(Auth::user()->id) + UserManager::unattended_comments(Auth::user()->id) + UserManager::unattended_remarks(Auth::user()->id);
      }
      
      public function afterSave(){
          
           if(!admin() && self::$unattended_items==0){
               $last_update = Auth::user()->last_update;
               if(trim($last_update=='')) $last_update = array();
               else $last_update = json_decode($last_update, true);
               $last_update[Session::get('program_id')] = date('Y-m-d H:i:s');
               $last_update = json_encode($last_update);
               User::where('id', Auth::user()->id)->update(array('last_update' => $last_update));
               //User::where('id', Auth::user()->id)->update(array('last_update' => date('Y-m-d H:i:s')));
           }
      }
      
      public static function mark($id, $field = 'read', $value=1){
           $answer = self::where('id', $id)->first();
           $answer->$field = $value;
           $answer->timestamps = false;
           $answer->save();
       }
       
       public static function mark_lesson($lesson, $user){
           $blocks = Block::where('lesson_id',$lesson)->lists('id');
           // mark answers as attended
           $answers = self::where('user_id', $user)->whereIn('block_id',$blocks)->get();
           foreach($answers as $answer){
               $answer->attended = 1;
               $answer->timestamps = false;
               $answer->save();
           }
           // mark answer comments as attended
           $block_answers = Block_answer::whereIn('block_id',$blocks)->lists('id');
           $comments = Conversation::whereIn('block_answer_id', $block_answers)->get();
           foreach($comments as $c){
               $c->attended = 1;
               $c->timestamps = false;
               $c->save();
           }
           // mark lesson comments as attended
           $comments = Conversation::where('user_id', $user)->where('lesson_id', $lesson)->where('posted_by','user')->get();
           foreach($comments as $c){
               $c->attended = 1;
               $c->timestamps = false;
               $c->save();
           }
       }
   
}
