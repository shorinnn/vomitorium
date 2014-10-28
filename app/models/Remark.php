<?php
use LaravelBook\Ardent\Ardent;

class Remark extends Ardent {
      public static $relationsData = array(
        'lesson' => array(self::BELONGS_TO, 'Lesson'),
        'user' => array(self::BELONGS_TO, 'User'),
        'admin' => array(self::BELONGS_TO, 'User'),
        'attachments' => array(self::HAS_MANY, 'Attachment')
      );
      
       public static $rules = array(
         'user_id' => 'required|numeric',
         'lesson_id' => 'required|numeric',
         'admin_id' => 'required|numeric',
         'remark' => 'required'
       );

       public function beforeSave(){
           //$this->read = 0;
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
}
