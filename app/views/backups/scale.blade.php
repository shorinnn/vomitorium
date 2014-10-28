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
    $answer_str = json_decode($answer->answer);
    $comments = Answer_comment::get_comments($answer->id, 2, 0);
    $unread = Answer_comment::unread($answer->id);
    $total = Answer_comment::total($answer->id);
    if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';
}
?>
<div class="block_div {{$extra_class}}" id='block-{{$block->id}}'>

<h2>{{$block->title}}</h2>

@if($answer==null)
    <div class="scale-holder scale-holder-{{$block->id}}">
@else
    <div class="scale-holder scale-holder-{{$block->id}}" style='display:none'>
@endif
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="active"><a id="tb1" href="#t1" data-toggle="tab">Step 1</a></li>
        <li><a id="tb2" href="#t2" data-toggle="tab">Step 2</a></li>
        <li><a id="tb3" href="#t3" data-toggle="tab">Step 3</a></li>
        <li><a id="tb4" href="#t4" data-toggle="tab">Step 4</a></li>
    </ul>
    <br />
    <div class='col-lg-{{$block->scale_max}}'>
        <div class='pull-left'>{{$block->scale_min_text}}</div>
        <div class='pull-right'>{{$block->scale_max_text}}</div>
    </div>

    <!-- Tab panes -->
    <div class="tab-content">

    <?php
    $entries = json_decode($block->scale_entries);
    $i = 1;
    $quarter = ceil(count($entries) / 4);
    $divs_open = true;
    foreach($entries as $e){
        $divs_open = true;
        $val = Str::slug($e);
        $class = "scale-$block->id-$val";
        if($i==1) echo '<div class="tab-pane active" id="t1">';
        if($i== $quarter*1+1 ) echo '<div class="tab-pane" id="t2">';
        if($i== $quarter*2+1 ) echo '<div class="tab-pane" id="t3">';
        if($i== $quarter*3+1 ) echo '<div class="tab-pane" id="t4">';
        ?>
    <div class='col-lg-12 text-justify {{$class}}' data-input-name='scale-{{$block->id}}-{{$val}}' data-entry-name="{{$e}}">
        <h4>{{$e}}</h4>
        <?php

            for($j=$block->scale_min; $j<=$block->scale_max; ++$j ){?>
            <div class='col-lg-1'><input type='radio'  name='scale-{{$block->id}}-{{$val}}' value='{{$j}}' /> {{$j}} </div>
            <?php } ?>
    </div>
        @if($i==$quarter*4 && !admin())
        <br /><button class="btn btn-primary" type="button" onclick="all_scales({{$block->id}})">Submit</button>
        @endif
        
<?php
        if($i==$quarter || ($i == $quarter*2) || ($i == $quarter*3)){
            $divs_open = false;
            echo '<br /><br /><br /><br />
                
                    <div class="text-center" style="padding-top:20px;">
                    <a class="btn btn-primary" style="border:1px solid blue; margin-top:20px" onclick="show_tab('.($i/$quarter+1).')">Next Step</a>
                    </div>
                    </div> ';
        }
        if($i == $quarter*4){
            $divs_open = false;
            echo '</div>';
        }
        ++$i;
}
    if($divs_open==true) echo "<br /><br /><br /><br />
        <div class='text-center' style='padding-top:20px;'>
            <button style='margin-top:20px;' class='btn btn-primary' type='button' onclick='all_scales($block->id)'>Submit</button>
                </div>";
?></div>
</div>
    </div>
    
    @if($answer_str!=null)
        <div id='scale_answer_area'>
        @foreach($answer_str as $a)
            {{$a->option}} (rated {{$a->rated}}) <br />
        @endforeach
        @if(!admin())
            <button type="button" id="edit_btn" class="btn btn-danger" onclick="edit_answers()">Edit Answers</button>
        @endif
        </div>
    @endif
    
    @if($comments)
        {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
    @endif
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}