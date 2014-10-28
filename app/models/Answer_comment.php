<?php
use LaravelBook\Ardent\Ardent;

class Answer_comment extends Ardent {
        public static $rules = array(
            'block_answer_id'=>'required|numeric'
        );
    
      public static $relationsData = array(
        'block_answer' => array(self::BELONGS_TO, 'Block_answer'),
        'user' => array(self::BELONGS_TO, 'User'),
        'attachments' => array(self::HAS_MANY, 'Attachment')
      );
       
       public static function get_comments($block_id, $limit=2, $offset = 0){
           if(!admin()){
               $block_id = Block_answer::where('user_id', Auth::user()->id)->where('id',$block_id)->count() == 0 ? 0 : $block_id;
           }
           return self::skip($offset)->take($limit)->where('block_answer_id', $block_id)->orderBy('id','desc')->get();
       }
       
       public function username(){
           if($this->user_id>0) return User::find($this->user_id)->username;
           else return User::find($this->admin_id)->username;
           if($avatar==''){
               
           }
       }
       
       public function user(){
           if($this->user_id>0) return User::find($this->user_id);
           else return User::find($this->admin_id);
       }
       
       public function user_avatar(){
           if($this->user_id>0) return User::find($this->user_id)->avatar;
           else return  User::find($this->admin_id)->avatar;
       }
       
       public static function unread($block_id){
           if(!admin()){
               $block_id = Block_answer::where('user_id', Auth::user()->id)->where('id',$block_id)->count() == 0 ? 0 : $block_id;
               return self::where('block_answer_id', $block_id)->where('read',0)->count();
           }
           return self::where('block_answer_id', $block_id)->where('attended',0)->count();
       }
       
       public static function total($block_id){
           if(!admin()){
               $block_id = Block_answer::where('user_id', Auth::user()->id)->where('id',$block_id)->count() == 0 ? 0 : $block_id;
           }
           
           return self::where('block_answer_id', $block_id)->count();
       }
       
       public static function mark($id, $block_id, $field = 'read', $value=1){
           if(!admin()){
               $block_id = Block_answer::where('user_id', Auth::user()->id)->where('id',$block_id)->count() == 0 ? 0 : $block_id;
           }
           $comment = self::where('id', $id)->where('block_answer_id',$block_id)->first();
           $comment->$field = $value;
           $comment->save();
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
