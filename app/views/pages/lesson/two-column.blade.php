<?php
if(Session::has('user_id'))    $answer = Block_answer::where('block_id',$block->id)->where('user_id',Session::get('user_id'))->first();
else     $answer = Block_answer::where('block_id',$block->id)->where('user_id',Auth::user()->id)->first();
?>
{{View::make('pages.lesson.new_submission')->withAnswer($answer)}}
<?php

$extra_class = '';
    $comments = array();
    $total = $unread = 0;
    $answer_str = null;
    $min_rows = $block->scale_min+1;
    if($answer!=null){
        $extra_class = 'answered_block';
        $array = json_decode($answer->answer, true);
        $min_rows = (count($array)+1 > $min_rows) ? count($array)+1 : $min_rows;
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
<table class="table two-column-table-{{$block->id}}">
    <thead>
        <tr><th>{{$block->scale_min_text}}</th><th>{{$block->scale_max_text}}</th></tr>
    </thead>
    <tbody>
        <?php
            $link_counter = 1;
        ?>
        @for($i=1; $i<$min_rows; ++$i)
        <tr>
            <?php
                $linked_attr_left = "data-link-order='$block->ord' data-link-class='linked-left-$i'";
            ?>
            <td><input data-block="{{$block->id}}" data-row="{{$i}}" type="text" {{$linked_attr_left}} 
                       class="linked-cell required form-control two-column two-column-{{$block->id}}" 
                  {{disable_answered($answer_str)}} name="two-column[{{$block->id}}][{{$i}}][1]" value="{{ $array[$i][1] or '' }}" /></td>
            <td><input data-block="{{$block->id}}" data-row="{{$i}}" type="text" 
                       class="required form-control two-column two-column-{{$block->id}}" 
                  {{disable_answered($answer_str)}} name="two-column[{{$block->id}}][{{$i}}][2]" value="{{ $array[$i][2] or '' }}"/></td>
        </tr>
        @endfor
    </tbody>
</table>


@if($comments)
    {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
@endif
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}