<?php
use LaravelBook\Ardent\Ardent;

class Block extends Ardent {
	public static $rules = array(
            'type'=>'required',
            'ord' => 'required',
            'lesson_id' => 'required|numeric'
        );
        
        public static $relationsData = array(
        'lesson' => array(self::BELONGS_TO, 'Lesson'),
        'block_answers' => array(self::HAS_MANY, 'Block_answer')
      );

        
        public function move($direction='up'){
        // can't move last item further down
        if($this->ord == DB::table('blocks')->max('ord') && $direction=='down') return;
        // can't move first item further up
        if($this->ord==1 && $direction=='up') return;
        // swap positions 
        if($direction=='up'){
            $other = Block::where('lesson_id', $this->lesson_id)->where('ord', $this->ord - 1)->first();
            $other->ord++;
            $this->ord--;
        }
        else{
            $other = Block::where('lesson_id', $this->lesson_id)->where('ord', $this->ord + 1)->first();
            $other->ord--;
            $this->ord++;
        }
        
        if(!$other->updateUniques()) {
            return format_validation_errors($other->errors()->all());
        }
        if(!$this->updateUniques()){
            return format_validation_errors($other->errors()->all());   
        }
    }  
    public function beforeDelete() {
         DB::table('blocks')->where('lesson_id', $this->lesson_id)->where('ord','>', $this->ord)->decrement('ord');               
         if($this->block_answers()->count() > 0){
             foreach($this->block_answers()->get() as $b){
                 $b->delete();
             }
         }
    }
      
      public static function top_skills($type = 'personality', $limit = 3){
          $type = strtolower($type);
          // get all the skill select blocks
          $functional = $personality = array();
          $lessons =  Lesson::where('program_id', Session::get('program_id'))->where('published',1)->lists('id');
          $blocks = Block::where('answer_type','Skill Select')->whereIn('lesson_id', $lessons)->lists('id');
          $user_id = (Session::has('user_id')) ? Session::get('user_id') : Auth::user()->id;
          if($blocks!='' && count($blocks)>0){
              // get the skill answers
              $answers = Block_answer::where('user_id',$user_id)->whereIn('block_id',$blocks)->get();
              foreach($answers as $a){
                  $data = json_decode($a->answer, true);
                  foreach($data['functional'] as $k => $v){
                      if(array_key_exists($v, $functional)){
                          $functional[$v] += 1;
                      }
                      else{
                          $functional[$v] = 1;
                      }
                  }
                  foreach($data['personality'] as $k => $v){
                      if(array_key_exists($v, $personality)){
                          $personality[$v] += 1;
                      }
                      else{
                          $personality[$v] = 1;
                      }
                  }
              }
          }
          arsort($$type);
          $$type = array_slice($$type, 0, $limit);
          return ($$type);
      }

}
