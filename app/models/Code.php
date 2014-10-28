<?php
use LaravelBook\Ardent\Ardent;

class Code extends Ardent {
    
	public static $rules = array(
            'program_id'=>'required',
            'code' =>'required|unique:codes'
        );
        
        public static $relationsData = array(
        'program' => array(self::BELONGS_TO, 'Program')
      );
        
        public function user(){
            return User::find($this->used_by);
        }
        
      
}
