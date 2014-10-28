<?php
if($block->answer_type=='Multiple Choice'){
    $choices = array();
    $json = json_decode($block->choices);
    foreach($json as $j){
        $val = Block_answer::where('user_id', Auth::user()->id)->where('block_id',$j->block_id)->first();
        if($val==null || $val->answer=='') continue;
        $choices[$val->answer] = $val->answer;
    }
}
else{
    $choices = DB::table('skills')->where('id',$block->skill_type)->first();
    $choices = explode(',',$choices->values);
}
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
?>
{{View::make('pages.lesson.new_submission')->withAnswer($answer)}}
<?php
$extra_class = '';
$comments = array();
$total = $unread = 0;
$answer_str = null;
$answer_class = '';
if($answer!=null && trim($answer->answer)!=''){
    if(admin()) $extra_class = 'answered_block';
    $answer_class = 'hidden';
    $answer_str = json_decode($answer->answer, true);
    $comments = Conversation::where('block_answer_id', $answer->id)->take(2)->orderBy('id','desc')->get();
    $unread = Conversation::unread_comments($answer->id);
    $total = Conversation::total_comments($answer->id);
    if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';
}
else $answer = null;
$i = 0;
?>
<div class="block_div {{$extra_class}}" id='block-{{$block->id}}'>

<h2 id="block-sortable-list-{{$block->id}}">{{$block->title}}</h2>
<h4 class='text-center sorting-instructions sorting-instructions-{{$block->id}}'>{{$block->subtitle}}</h4>
<h4 class='text-center black-txt sorting-instructions sorting-instructions-{{$block->id}}'>{{$block->scale_max_text}}</h4>
@if($answer!=null)
    <div class="scale-answered-{{$block->id}}">
        @foreach($answer_str  as $a)
            {{$a['option']}}<br />
        @endforeach<br />
        <div class='text-center'>
            <button type='button' class="btn btn-primary" onclick="edit_sortable({{$block->id}})">Edit Answer</button>
        </div>
    </div>
    
@endif
<div class="functional-skills sortable-list-holder-{{$block->id}} {{$answer_class}}">

@if($block->maximum_choices>0)
    @if($answer!=null)
    <input class='mc_validation-disabled' type="hidden" data-mc-block="sortable-list-{{$block->id}}"  data-mc-min="{{$block->minimum_choices}}" 
       data-mc-max="{{$block->maximum_choices}}" 
       data-mc-message="You must pick between  {{$block->minimum_choices}} and {{$block->maximum_choices}} options and click the Sort button." />
    @else
    <input class='mc_validation' type="hidden" data-mc-block="sortable-list-{{$block->id}}"  data-mc-min="{{$block->minimum_choices}}" 
       data-mc-max="{{$block->maximum_choices}}" 
       data-mc-message="You must pick between  {{$block->minimum_choices}} and {{$block->maximum_choices}} options and click the Sort button." />
    @endif

@else
@endif

@foreach($choices as $c)
   <?php 
        ++$i;
       $c = trim($c);
       $checked = '';
    ?>
    <div class="skill-dv">
        <input type="checkbox" class='sortable-list-{{$block->id}}' value="{{$c}}" id="list-{{$block->id}}-{{$i}}" /> 
        <label for="list-{{$block->id}}-{{$i}}">{{$c}}</label></div>
      @endforeach
	<span class="cl"></span>
        <p class="text-center">
            <button type="button" class="btn btn-success" onclick="do_sortable_list({{$block->id}})">Sort Selected</button>
        </p.>
</div>
<h4 class='text-center black-txt sorting-instructions sorting-instructions-{{$block->id}}'>{{$block->scale_min_text}}</h4>
@if($comments)
    {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
@endif
<span class="cl"></span>
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}