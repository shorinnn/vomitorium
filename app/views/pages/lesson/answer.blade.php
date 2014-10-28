<?php
    $answer = Block::find($block->answer_id);
    $content = $title = '';
    $user_id = Session::has('user_id') ? Session::get('user_id') : Auth::user()->id;
    $str = Block_answer::where('block_id',$answer->id)->where('user_id', $user_id)->first();
    if($answer->answer_type=='Open Ended'){
        if($str!=null){
            $title = $answer->title;
            if($answer->type=='sortable'){
               $str = json_decode($str->answer, true);
               foreach($str  as $a){
                    $content.= "$a[option]<br />";
               }
           }
           else{  $content .=  $str->answer;}
       }
    }
    elseif($answer->answer_type=='Multiple Choice'){
        if($str!=null){
            $title = $answer->title;
            $str = json_decode($str->answer, true);
            $content.= implode('<br />', $str);
        }
    }
    
    else{
        if($str!=null){
            $title = $answer->title;
            $str = json_decode($str->answer, true);
            $str = $str[$block->skill_type];
            $actual_answer = Block_answer::where('block_id', $block->id)->where('user_id', $user_id)->first();
            if($actual_answer==null) $answer_str =  null;
            else $answer_str = json_decode($actual_answer->answer, true);
            foreach($str as $s){
                $val = '';
                if($answer_str!=''){
                    $val = $answer_str[$s];
                }
                $content.= "$s<br />";
                $content.= "<input type='text' value='$val' class='form-control' name='followup[$block->id][$s]' ".disable_answered($answer_str)." required/>";
            }
        }
    }
    ?>
@if(trim($content)!='')
    <blockquote>{{$content}}
    </blockquote>
@endif