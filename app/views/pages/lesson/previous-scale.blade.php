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
    $extra_class = 'answered_block';
    $comments = Conversation::where('block_answer_id', $answer->id)->take(2)->orderBy('id','desc')->get();
    $unread = Conversation::unread_comments($answer->id);
    $total = Conversation::total_comments($answer->id);
    $answer_str = $answer->answer;
    if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';
}
?>
<div class="block_div {{$extra_class}}" id='block-{{$block->id}}'>

    <h2>{{$block->title}}</h2>


<!-- inputs -->
<?php
$answer = Block::find($block->answer_id);
$user_id = Session::has('user_id') ? Session::get('user_id') : Auth::user()->id;
$str = Block_answer::where('block_id',$answer->id)->where('user_id', $user_id)->first();
 if($str!=null){
    $str = json_decode($str->answer);
    $actual_answer = Block_answer::where('block_id', $block->id)->where('user_id', $user_id)->first();
    if($actual_answer==null) $answer_str =  null;
    else $answer_str = json_decode($actual_answer->answer, true);
    foreach($str as $s){
        $val = '';
        if($answer_str!=''){
            $val = $answer_str[$s->option];
        }
        echo "$s->option (rated $s->rated)<br />";
        //echo "<input type='text' value='$val' class='form-control' name='followup[$block->id][$s->option]' ".disable_answered($answer_str)." required/>";
    }
}
?>

<!-- end of inputs -->
<br />
@if($comments)
    {{View::make('pages.comment_form')->withAnswer($actual_answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
@endif
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}