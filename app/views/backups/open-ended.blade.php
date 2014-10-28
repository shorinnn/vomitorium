<?php
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
//$block_class = get_block_class($answer);
?>
<div class="block_div" id='block-{{$block->id}}'>
    
    
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


@if($block->required)
<textarea class="form-control required" name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}} required>{{$answer_str}}</textarea> <span class="required_label">*required</span>
@else
<textarea class="form-control" name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}}>{{$answer_str}}</textarea>
@endif   
<br />
@if($comments)
    {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
@endif
</div>