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
    $comments = Conversation::where('block_answer_id', $answer->id)->take(2)->orderBy('id','desc')->get();
    $unread = Conversation::unread_comments($answer->id);
    $total = Conversation::total_comments($answer->id);
    $answer_str = $answer->answer;
    if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';
}
?>
<div class="block_div open-block {{$extra_class}}" id='block-{{$block->id}}'>


    <h2>{{$block->title}}</h2>
    @if(trim($block->subtitle)!='')
        <h4>{{$block->subtitle}}</h4>
    @endif


@if($block->required)
    @if($block->scale_min_text=='one')
        <input type='text' class="form-control blue-border required" name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}} value="{{$answer_str}}" required /> <span class='required_label'>*required</span>
    @elseif($block->scale_min_text=='short')
       <textarea class="form-control white-textarea required" name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}} required>{{$answer_str}}</textarea> <span class='required_label'>*required</span>
    @else
       <textarea class="form-control white-textarea essay required" name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}} required>{{$answer_str}}</textarea> <span class='required_label'>*required</span>
    @endif

@else
    @if($block->scale_min_text=='one')
        <input type='text' class="form-control blue-border " name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}} value="{{$answer_str}}" /> 
    @elseif($block->scale_min_text=='short')
       <textarea class="form-control white-textarea " name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}}>{{$answer_str}}</textarea> 
    @else
       <textarea class="form-control essay white-textarea " name='open_answer[{{$block->id}}]' id='open-answer-{{$block->id}}' {{disable_answered($answer_str)}}>{{$answer_str}}</textarea> 
    @endif
@endif   
<br />
@if($comments)
    {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
@endif
</div>
 {{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}