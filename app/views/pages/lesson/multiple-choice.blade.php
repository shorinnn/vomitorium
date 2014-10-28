<?php
$choices = json_decode($block->choices);
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else     $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
?>
{{View::make('pages.lesson.new_submission')->withAnswer($answer)}}
<?php
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

<h2>{{$block->title}}</h2>
<h4>{{$block->subtitle}}</h4>
<input class='mc_validation' type="hidden" data-mc-block="{{$block->id}}"  data-mc-min="{{$block->minimum_choices}}" 
       data-mc-max="{{$block->maximum_choices}}" 
       @if($block->minimum_choices>0 && $block->maximum_choices==99999)
           data-mc-message="You must pick at least {{$block->minimum_choices}} {{singplural($block->minimum_choices,'options')}} for '{{$block->title}}'"
       @else
           data-mc-message="You must pick between  {{$block->minimum_choices}} and {{$block->maximum_choices}} options for '{{$block->title}}'"
       @endif/>



<?php
$input_type = ($block->minimum_choices==1 && $block->maximum_choices==1) ? 'radio' :  'checkbox';
    foreach($choices as $c){
        $c = trim($c);
        $checked = '';
        if($answer!=null){
            if(in_array($c,$array)) $checked = 'checked="checked"';
        }
        $id = 'mc-answer-'.$block->id.'-'.Str::slug($c);
        ?>
        @if($input_type=='radio')
         <input {{disable_answered($answer_str)}}  {{$checked}} type='{{$input_type}}'  id={{$id}}
            name='mc-answer[{{$block->id}}]["{{$block->id}}"]' class='mc-answer-{{$block->id}}' 
       value='{{$c}}' /> <label for="{{$id}}">{{$c}}</label><br />
        @else
            <input {{disable_answered($answer_str)}}  {{$checked}} type='{{$input_type}}'  id={{$id}}
                name='mc-answer[{{$block->id}}]["{{$c}}"]' class='mc-answer-{{$block->id}}' 
           value='{{$c}}' /> <label for="{{$id}}">{{$c}}</label><br />
        @endif
    <?php
    }
    ?>
        @if($comments)
            {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
        @endif
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}