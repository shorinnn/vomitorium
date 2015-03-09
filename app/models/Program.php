<?php
use LaravelBook\Ardent\Ardent;

class Program extends Ardent {
        public static $rules = array(
            'name' =>'required|unique:programs'
        );
    
	public static $relationsData = array(
        
        'lessons' => array(self::HAS_MANY, 'Lesson'),
        'announcements' => array(self::HAS_MANY, 'Announcement'),
        'chapters' => array(self::HAS_MANY, 'Chapter'),
        'codes' => array(self::HAS_MANY, 'Code')
      );
        
        public static function json_values(){
            $arr = array();
            foreach(Program::all() as $p){
                $arr[$p->id] = $p->name;
            }
            return json_encode($arr);
        }
        
         public function beforeDelete() {
         // delete associated lessons
          if($this->lessons()->count() > 0){
             foreach($this->lessons()->get() as $l){
                 $l->delete();
             }
          }
         // delete associated blocks         
         if($this->chapters()->count() > 0){
             foreach($this->chapters()->get() as $c){
                 $c->delete();
             }
         }
         
         DB::table('payment_plans')->where('program_id', $this->id)->delete();
         DB::table('programs_users')->where('program_id', $this->id)->delete();
      }
      
      public function today_users(){
          $today = date("Y-m-d");
          $now = date("Y-m-d H:i:s");
           $expired = "(`expires` IS NULL  OR `expires` > '$now')";
          return DB::table('programs_users')->whereRaw($expired)->whereNull('subscription_cancelled')
                  ->where('program_id', $this->id)->whereRaw("DATE(`start_date`) = '$today'")->count();
      }
      
      public function newest_users($limit = 5){
          $now = date("Y-m-d H:i:s");
          $expired = "(`expires` IS NULL  OR `expires` > '$now')";
          $newest = DB::table('programs_users')->whereRaw($expired)->whereNull('subscription_cancelled')->whereNotNull('start_date')
                  ->where('program_id', $this->id)->orderBy('start_date','DESC')->paginate($limit);
          return $newest;
          
      }
      
      public function active_announcements(){
          return DB::table('announcements')->where('program_id', $this->id)->where('published',1)
                  ->whereRaw("`start_date` <= '".date('Y-m-d')."'")->whereRaw("`end_date` >= '".date('Y-m-d')."'")->get();
      }
      
      public function users(){
          $now = date("Y-m-d H:i:s");
          $expired = "(`expires` IS NULL  OR `expires` > '$now')";
          $user = DB::table('programs_users')->whereNull('subscription_cancelled')->whereNotNull('start_date')
                  ->whereRaw($expired)->where('program_id', $this->id)->orderBy('start_date','DESC')->get();
          $users = new Illuminate\Database\Eloquent\Collection($user);
          return $users;
      }
}
