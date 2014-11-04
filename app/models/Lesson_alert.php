<?php
use LaravelBook\Ardent\Ardent;

class Lesson_alert extends Ardent {
	public static $rules = array(
            'lesson_id'=>'required',
            'type' => 'required'
        );
        
        public static $relationsData = array(
           'lesson' => array(self::BELONGS_TO, 'Lesson')
       );
        
          
}
