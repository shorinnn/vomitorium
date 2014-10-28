<?php
use LaravelBook\Ardent\Ardent;

class Conversation extends Ardent {
      public static $relationsData = array(
        'lesson' => array(self::BELONGS_TO, 'Lesson'),
        'block_answer' => array(self::BELONGS_TO, 'Block_answer'),
        'user' => array(self::BELONGS_TO, 'User'),
        'admin' => array(self::BELONGS_TO, 'User'),
        'attachments' => array(self::HAS_MANY, 'Attachment')
      );
      
       public static $rules = array(
         'user_id' => 'required|numeric',
         'lesson_id' => 'numeric',
         'block_answer_id' => 'numeric',
         'admin_id' => 'required|numeric',
         'content' => 'required'
       );
       
       public function beforeSave(){
           $this->program_id = Session::get('program_id');
       }
       
        public function afterSave(){
           if(!admin()){
               $last_update = Auth::user()->last_update;
               if(trim($last_update=='')) $last_update = array();
               else $last_update = json_decode($last_update, true);
               $last_update[Session::get('program_id')] = date('Y-m-d H:i:s');
               $last_update = json_encode($last_update);
               User::where('id', Auth::user()->id)->update(array('last_update' => $last_update));
           }
      }
      
      public function poster(){
          if($this->posted_by=='admin') return $this->admin;
          else return $this->user;
      }
      
      public static function unread_comments($block_id){
           if(!admin()) return self::where('block_answer_id', $block_id)->where('read',0)->count();
           return self::where('block_answer_id', $block_id)->where('attended',0)->count();
      }
      
      public static function total_comments($block_id){
           if(!admin()) return self::where('block_answer_id', $block_id)->count();
           return self::where('block_answer_id', $block_id)->count();
       }
       
       public function block_answer(){
           return Block_answer::find($this->block_answer_id);
       }
}
