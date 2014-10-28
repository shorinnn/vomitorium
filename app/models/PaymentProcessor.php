<?php
use LaravelBook\Ardent\Ardent;

class PaymentProcessor extends Ardent {
	protected $fillable = array('program_id');
        
        public static $rules = array(
         'program_id' => 'required|numeric',
         'name' => 'required',
         'field' => 'required'
       );
        
        public static $relationsData = array(
        'program' => array(self::BELONGS_TO, 'Program')
      );

}
