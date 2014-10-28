<?php

if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();


?>
{{View::make('pages.lesson.new_submission')->withAnswer($answer)}}
<?php
$extra_class = '';
$comments = array();
$total = $unread = 0;
$answer_str = null;

if($answer!=null){
    if(admin()) $extra_class = 'answered_block';
    $answer_str = json_decode($answer->answer, true);
    $functional = (isset($answer_str["functional"])) ? $answer_str["functional"] : array();
    $personality = (isset($answer_str["personality"])) ? $answer_str["personality"] : array();
    $comments = Conversation::where('block_answer_id', $answer->id)->take(2)->orderBy('id','desc')->get();
    $unread = Conversation::unread_comments($answer->id);
    $total = Conversation::total_comments($answer->id);
    if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';
}
$i = 0;

?>
<div class="block_div {{$extra_class}}" id='block-{{$block->id}}'>
<h2>{{$block->title}}</h2>
<div class="functional-skills">

<h3 id="block-{{$block->id}}-f">Functional Skills</h3>
@if($block->maximum_choices>0)
<input class='mc_validation' type="hidden" data-mc-block="{{$block->id}}-f"  data-mc-min="{{$block->minimum_choices}}" 
       data-mc-max="{{$block->maximum_choices}}" 
       data-mc-message="You must pick between  {{$block->minimum_choices}} and {{$block->maximum_choices}} functional skills." />
@else
@endif

   @foreach($skills['functional'] as $p)

   <?php 
        ++$i;
       $p = trim($p);
       $checked = '';
       if($answer_str!=null){
           if(in_array($p,$functional)) $checked = 'checked="checked"';
        }
    ?>
    <div class="skill-dv"><input  {{disable_answered($answer_str)}}  {{$checked}}  type="checkbox" class='mc-answer-{{$block->id}}-f' name='skill-answer[{{$block->id}}][functional][]' value="{{$p}}" id="skill-{{$block->id}}-{{$i}}" /> <label for="skill-{{$block->id}}-{{$i}}">{{$p}}</label></div>
      @endforeach
	<span class="cl"></span>
        
</div>

<div class="functional-skills">
    <br />

<h3 id="block-{{$block->id}}-p">Personality Skills</h3>
@if($block->maximum_choices>0)
<input class='mc_validation' type="hidden" data-mc-block="{{$block->id}}-p"  data-mc-min="{{$block->minimum_choices}}" 
       data-mc-max="{{$block->maximum_choices}}" 
       data-mc-message="You must pick between  {{$block->minimum_choices}} and {{$block->maximum_choices}} personality skills." />
@else

@endif

    @foreach($skills['personality'] as $p)

    <?php 
     ++$i;
    $p = trim($p);
    $checked = '';
   if($answer_str!=null){
       if(in_array($p,$personality)) $checked = 'checked="checked"';
    }
       
    ?>

    <div class="skill-dv"><input  {{disable_answered($answer_str)}}  {{$checked}} type="checkbox"  class='mc-answer-{{$block->id}}-p' id='skill-{{$block->id}}-{{$i}}' name='skill-answer[{{$block->id}}][personality][]' value="{{$p}}" /> <label for="skill-{{$block->id}}-{{$i}}">{{$p}}</label></div>


      @endforeach
      
	<span class="cl"></span>
</div>

@if($comments)

    {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}

@endif

<span class="cl"></span>
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}