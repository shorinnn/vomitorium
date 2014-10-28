<?php
use LaravelBook\Ardent\Ardent;

class PaymentPlan extends Ardent {
        public static $rules = array(
         'program_id' => 'required|numeric',
         'name' => 'required',
         'type' => 'required'
       );
        
        protected $fillable = array('program_id', 'name', 'type', 'cost', 'subscription_duration', 'subscription_duration_unit',
            'trial_cost', 'trial_duration', 'trial_duration_unit');
        
        public static $relationsData = array(
        'program' => array(self::BELONGS_TO, 'Program')
      );
        
    public function clients(){
        $now = date("Y-m-d H:i:s");
        $expired = "(`expires` IS NULL  OR `expires` > '$now')";
        return DB::table('programs_users')->whereRaw($expired)->where('subscription_id',$this->id)->whereNull('subscription_cancelled')->count();
    }
    
    public function cancelled(){
        return DB::table('programs_users')->where('subscription_id',$this->id)->whereNotNull('subscription_cancelled')->count();
    }

}
