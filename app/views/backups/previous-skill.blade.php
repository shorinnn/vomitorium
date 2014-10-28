<?php
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
$block_class = get_block_class($answer);
?>
<div class="block_div {{$block_class}}" id='block-{{$block->id}}'>
    <h2>{{$block->title}}</h2>
<?php

$comments = array();
$total = $unread = 0;
$answer_str = null;
if($answer!=null){
    $comments = Answer_comment::get_comments($answer->id, 2, 0);
    $unread = Answer_comment::unread($answer->id);
    $total = Answer_comment::total($answer->id);
    $answer_str = $answer->answer;
}
?>
@if($answer!=null)
    @if(admin())
        @if($answer->attended==0)
        <p class="text-right unattended_item">
            <span class='' id='unattended-warning-{{$answer->id}}'><i class='glyphicon glyphicon-exclamation-sign'></i> Submission Unattended</span>
            <button id="mark-s-read-{{$answer->id}}" type="button" class="btn btn-success btn-xs" onclick="mark_submission_attended({{$answer->id}})">Mark as attended</button>
        @else
        <p class="text-right">
            <button id="mark-s-read-{{$answer->id}}" type="button" class="btn btn-danger btn-xs" onclick="mark_submission_unattended({{$answer->id}})">Mark as unattended</button>
        @endif
    @endif
</p>
@endif 


<!-- inputs -->
<?php
$answer = Block::find($block->answer_id);
$user_id = Session::has('user_id') ? Session::get('user_id') : Auth::user()->id;
$str = Block_answer::where('block_id',$answer->id)->where('user_id', $user_id)->first();
 if($str!=null){
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
        echo "$s<br />";
        echo "<input type='text' value='$val' class='form-control' name='followup[$block->id][$s]' ".disable_answered($answer_str)." required/>";
    }
}
?>

<!-- end of inputs -->
<br />
@if($comments)
    {{View::make('pages.comment_form')->withAnswer($actual_answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
@endif
</div>