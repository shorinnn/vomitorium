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
    $comments = Conversation::where('block_answer_id', $answer->id)->take(2)->orderBy('id','desc')->get();
    $unread = Conversation::unread_comments($answer->id);
    $total = Conversation::total_comments($answer->id);
    if($comments!=array() && $comments->count()>0) $extra_class.= ' commented_block';
}
?>
<div class="block_div scale-block {{$extra_class}}" id='block-{{$block->id}}'>
<div class="q-icon">Q</div>
<h2>{{$block->title}}</h2>

@if($answer==null)
    <div class="scale-holder scale-holder-{{$block->id}}">
@else
    <div class="scale-holder scale-holder-{{$block->id}}" style='display:none'>
@endif
<?php
    $entries = json_decode($block->scale_entries);
    $total = count($entries);
    $per_page = $block->minimum_choices;
    if($per_page<1) $per_page = $total;
    $tabs = ceil($total / $per_page);
    ?>
    <!-- Nav tabs -->
    @if($tabs>1)
        <ul class="nav nav-tabs">
            @for($i=1;$i<=$tabs;++$i)
                @if($i==1)
                  <li class="active"><a id="tb1" href="#t1" data-toggle="tab">Step 1</a></li>
                @else
                  <li><a id="tb{{$i}}" href="#t{{$i}}" data-toggle="tab">Step {{$i}}</a></li>
                @endif
            @endfor
        </ul>
    @endif
    <br />
    <div class='col-lg-{{$block->scale_max}} scale-ends-label'>
        <div class='pull-left'>{{$block->scale_min_text}}</div>
        <div class='pull-right'>{{$block->scale_max_text}}</div>
    </div>

    <!-- Tab panes -->
    <div class="tab-content">   

    <?php
    $i = 1;
    $quarter = ceil(count($entries) / 4);
    $divs_open = true;
    $group = 1;
    $groups = array();
    foreach($entries as $e){
        $groups[$group][] = $e;
        ++$i;
        if($i > $per_page){
            $group++;
            $i = 1;
        }
    }
    
    foreach($groups as $key=>$group){
        $active = ($key==1) ? 'active' : '';
        echo '<div class="tab-pane '.$active.'" id="t'.$key.'">';
        $used_page  =0;
         foreach($group as $e){
            $divs_open = true;
            $val = Str::slug($e);
            $class = "scale-$block->id-$val";
            ?>
            <div class='col-lg-12 text-justify {{$class}}' data-input-name='scale-{{$block->id}}-{{$val}}' data-entry-name="{{$e}}">
            <h4>{{$e}}</h4>
            <?php

                for($j=$block->scale_min; $j<=$block->scale_max; ++$j ){?>
                <div class='col-lg-1 scale-value'>
                    <input type='radio'  name='scale-{{$block->id}}-{{$val}}' id='scale-{{$block->id}}-{{$val}}-{{$j}}' value='{{$j}}' /> 
                    <label for='scale-{{$block->id}}-{{$val}}-{{$j}}'>{{$j}}</label> </div>
                <?php } ?>
            </div>
            <?php
            $used_page++;
        }
        if($used_page<$per_page){
            for($z=0;$z<($per_page-$used_page); ++$z){
                echo "
                <div class='col-lg-12 text-justify invisible'>
                    <h4> !</h4><div class='col-lg-1 scale-value'><input type='radio' /><label> !</label></div>
                </div>";
            }
        }
        echo '<!--'.$used_page.'--><center>';
        if($key < count($groups)){
            echo '<span class="clearfix clear_fix"></span><a class="btn btn-primary" style="border:1px solid blue; margin-top:20px" onclick="show_tab('.($key+1).')">Next Step</a>';
        }
        else{
            if(!admin()) echo "<span class='clearfix clear_fix'></span> <button style='margin-top:20px;' class='btn btn-primary' type='button' onclick='all_scales($block->id)'>Submit</button>";
            else  echo '<span class="clearfix clear_fix"></span><a class="btn btn-primary invisible" style="border:1px solid blue; margin-top:20px" onclick="show_tab('.($key+1).')">Next Step</a>';
        }
        echo '</center>';
        echo '</div>';
    }
   ?>
        <span class='clearfix' style='clear:both'></span>
    </div>

    </div>
    
    @if($answer_str!=null)
        <div id='scale_answer_area'>
        @foreach($answer_str as $a)
            {{$a->option}} (rated {{$a->rated}}) <br />
        @endforeach

        </div>
    @endif
    
    @if($comments)
        {{View::make('pages.comment_form')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}
    @endif
</div>
{{View::make('pages.reply')->withAnswer($answer)->withTotal($total)->withComments($comments)->withUnread($unread)}}