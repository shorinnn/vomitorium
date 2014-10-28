<?php
use LaravelBook\Ardent\Ardent;

class Announcement extends Ardent {
	public static $rules = array(
            'program_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
            'content' => 'required'
        );
        
      public static $relationsData = array(
        'program' => array(self::BELONGS_TO, 'Program')
      );

        
      
}
