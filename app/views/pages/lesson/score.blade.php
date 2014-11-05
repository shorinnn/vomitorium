<?php
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else     $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
?>
{{View::make('pages.lesson.new_submission')->withAnswer($answer)}}
<?php
$choices =  json_decode($block->choices, true);
$total_score = count($choices) * $block->scale_max;
$extra_class = '';
    $comments = array();
    $total = $unread = 0;
    $answer_str = null;
    if($answer!=null){
        $extra_class = 'answered_block';
        $array = json_decode($answer->answer, true);
        $answer_str = $answer->answer;
        $comments = Conversation::where('block_answer_id', $answer->id)->take(2)->orderBy('id','desc')->get();
        $unread = Conversation::unread_comments($answer->id);
        $total = Conversation::total_comments($answer->id);
        if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';

    }?>
<div class="block_div {{$extra_class}}" id='block-{{$block->id}}'>
<div class="q-icon">Q</div>
<h2>{{$block->title}}</h2>
<h4>{{$block->subtitle}}</h4>
@foreach($choices as $c)
<div class='row row-no-margin'>
    <div class='col-lg-11'>{{$c}}</div><div class='col-lg-1'>
        <input type='text' name="independent_score[{{$block->id}}][{{$c}}]" data-min='{{$block->scale_min}}' data-max='{{$block->scale_max}}' 
       data-block='{{$block->id}}' class='form-control block-score required'
       @if($answer_str!=null)
       value="{{$array['independent_scores'][$c]}}"
       disabled="disabled"
       @endif
       /></div>
</div>  
@endforeach
<div class='row row-no-margin'>
    <div class='col-lg-9'>Total Score out of {{$total_score}}</div>
    <div class='col-lg-3'>
        <input type='text' readonly="readonly" data-block='{{$block->id}}' name="score[{{$block->id}}]" class='form-control block-score-total'
               @if($answer_str!=null)
               value="{{$array['score']}}"
               disabled="disabled"
               @endif
               />
    </div>
</div>

        @if($comments)
            {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
        @endif
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}