<?php
Validator::extend('alone_in_program', function($attribute, $value, $parameters)
{
    return true;
        $chapter = DB::table('chapters')->where('program_id', Session::get('program_id'))->where($attribute, $value)->first();
        if($chapter==null) return true;
        else return false;
});

use LaravelBook\Ardent\Ardent;


class Chapter extends Ardent {
    
    public static $rules = array(
     'title' => 'required|alone_in_program',
     'ord' => 'required'
   );
    
     public static $relationsData = array(
        'lessons' => array(self::HAS_MANY, 'Lesson')
      );
     
     
    
    public function move($direction='up'){
        // can't move last item further down
        if($this->ord == DB::table('chapters')->where('program_id',Session::get('program_id'))->max('ord') && $direction=='down') return;
        // can't move first item further up
        if($this->ord==1 && $direction=='up') return;
        // swap positions 
        if($direction=='up'){
            $other = Chapter::where('ord', $this->ord - 1)->where('program_id',Session::get('program_id'))->first();
            $other->ord++;
            $this->ord--;
        }
        else{
            $other = Chapter::where('ord', $this->ord + 1)->where('program_id',Session::get('program_id'))->first();
            $other->ord--;
            $this->ord++;
        }
        
        if(!$other->updateUniques()) {
            
            return format_validation_errors($other->errors()->all());
        }
        if(!$this->updateUniques())             return format_validation_errors($other->errors()->all());   
    }
    
     public function afterDelete() {
        // update the order for remaining chapters
        DB::table('chapters')->where('program_id',Session::get('program_id'))->where('ord','>', $this->ord)->decrement('ord');
        // unlink lessons
        //DB::table('lessons')->where('chapter_id',$this->id)->update(array('chapter_id'=>0));
        //DELETE LESSONS!
        DB::table('lessons')->where('chapter_id',$this->id)->delete();
      }
      
      public function afterSave(){
          DB::table('lessons')->where('chapter_id',$this->id)->update(array('chapter_ord'=>$this->ord));
      }
      
      public static function move_after($move, $target){
          if($move==$target) return;
            if($target==0){
                $move = Chapter::find($move);
                if($move==null) return;
                if($move->ord==1) return;
                DB::table('chapters')->where('program_id', $move->program_id)->where('ord','>', $move->ord)->decrement('ord');
                DB::table('chapters')->where('program_id', $move->program_id)->increment('ord');
                $move->ord = 1;
            }
            else{
                $move = Chapter::find($move);
                $target = Chapter::find($target);
                if($move==null || $target==null) return;
                if($move->program_id != $target->program_id) return;
                if($move->ord == $target->ord+1) return;
                $target_ord = $target->ord;
                $move_ord = $move->ord;
                DB::table('chapters')->where('program_id', $move->program_id)->where('ord','>', $move->ord)->decrement('ord');
                $move = Chapter::find($move->id);
                $target = Chapter::find($target->id);
                $move->ord = $target->ord + 1;
                DB::table('chapters')->where('program_id', $move->program_id)->where('ord','>=', $move->ord)->increment('ord');
            }
            $move->updateUniques();
            foreach(Chapter::where('program_id', $move->program_id)->get() as $c){
                $set = array('chapter_ord'=> $c->ord);
                DB::table('lessons')->where('chapter_id', $c->id)->update($set);
            }
      }
}