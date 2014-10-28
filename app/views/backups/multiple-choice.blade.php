<?php
$choices = json_decode($block->choices);
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else     $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
$block_class = get_block_class($answer);
?>
<div class="block_div {{$block_class}}" id='block-{{$block->id}}'>
@if($answer!=null)
    @if(admin())
        @if($answer->attended==0)
        <p class="text-right unattended_item">
            <span id='unattended-warning-{{$answer->id}}'><i class='glyphicon glyphicon-exclamation-sign'></i> Submission Unattended</span>
            <button id="mark-s-read-{{$answer->id}}" type="button" class="btn btn-primary btn-xs" onclick="mark_submission_attended({{$answer->id}})">Mark as attended</button>
        @else
        <p class="text-right">
            <button id="mark-s-read-{{$answer->id}}" type="button" class="btn btn-primary btn-xs" onclick="mark_submission_unattended({{$answer->id}})">Mark as unattended</button>
        @endif
    @endif
</p>
@endif 
<h2>{{$block->title}}</h2>
<input class='mc_validation' type="hidden" data-mc-block="{{$block->id}}"  data-mc-min="{{$block->minimum_choices}}" 
       data-mc-max="{{$block->maximum_choices}}" 
       data-mc-message="You must pick between  {{$block->minimum_choices}} and {{$block->maximum_choices}} options for '{{$block->title}}'" />
<?php
    
    
    $comments = array();
    $total = $unread = 0;
    $answer_str = null;
    if($answer!=null){
        $array = json_decode($answer->answer, true);
        $answer_str = $answer->answer;
        $comments = Answer_comment::get_comments($answer->id, 2, 0);
        $unread = Answer_comment::unread($answer->id);
        $total = Answer_comment::total($answer->id);
    }?>


<?php
    foreach($choices as $c){
        $c = trim($c);
        $checked = '';
        if($answer!=null){
            if(in_array($c,$array)) $checked = 'checked="checked"';
        }
        
        ?>
        <input {{disable_answered($answer_str)}}  {{$checked}} type='checkbox' name='mc-answer[{{$block->id}}]["{{$c}}"]' class='mc-answer-{{$block->id}}' 
       value='{{$c}}' /> <label for="">{{$c}}</label><br />

    <?php
    }
    ?>
        @if($comments)
            {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
        @endif
</div>