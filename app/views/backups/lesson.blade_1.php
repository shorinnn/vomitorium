@extends($layout)

@section('content')
<?php
$total_answers = 0;
$next_lesson_btn = '';
?>
@if(admin())
<script> var needs_edit = true;</script>
@else
<script> var needs_edit = false;</script>
@endif
<div class="section lesson-content">

<!-- progress bar --><br />
<p class="progress-bar-info">Your Progress</p>
<div class="progress" data-progress="{{$lesson_progress}}">
  <div class="bar" style="width:{{$lesson_progress}}%">{{$lesson_progress}}%</div>
</div>
<!-- /progress bar -->

        <div class="containerdeprecated">
            
            

            
            @if($unread_remark && !admin())
            @endif 
            
            <form id="lesson_form" action="{{Request::url()}}" method="post" novalidate>
                <div class='nav_btns'>
                <div class="pull-left">
                    <?php
                     $prev = previous_lesson($lesson);
                     if($prev!=''){
                         echo " <a class='btn btn-success' href='".URL('lesson/'.$prev)."'><i class='glyphicon glyphicon-backward'></i> Previous lesson</a>";
                     }
                    ?>
                </div>
                @if(Session::has('success'))
                    <p class='alert alert-success text-center'>{{Session::get('success')}}</p>
                    <?php
                       
                        
                        
                        $next_lesson = next_lesson($lesson);
                        
                        if($next_lesson!='') $next_lesson_btn =  "<div class='text-center next-lesson-btn'>
                                    <a class='btn btn-success' href='".URL('lesson/'.$next_lesson)."'>Next lesson <i class='glyphicon glyphicon-forward'></i></a>
                                </div>";
                        echo $next_lesson_btn;
                    ?>
                @else
                     @if($lesson->all_answered())
                     <?php
                      $next_lesson = next_lesson($lesson);
                      if($next_lesson!='') $next_lesson_btn =  "<div class=' next-lesson-btn'>
                                    <a class='btn btn-success' href='".URL('lesson/'.$next_lesson)."'>Next lesson <i class='glyphicon glyphicon-forward'></i></a>
                                </div>";
                        echo "<div class='text-right'>$next_lesson_btn</div>";
                        ?>
                     @endif
                @endif
                <br style='clearfix' />
        </div>
                @if(Session::has('error'))
                    <p class='alert alert-danger'>{{Session::get('error')}}</p>
                @endif
                <!--<h1 class="lesson-title">{{$lesson->title}}</h1>-->
<!--                <br style='clearfix' />
                <br style='clearfix' />-->
            <?php
            $page_has_submit = false;
            $prev_block = '';
                $page_has_scale = '';
                // display blocks that are not included in sections first
                foreach($lesson->blocks()->where('in_section',0)->orderBy('ord','ASC')->get() as $block){
                    if($block->type == 'text'){
                        $prev_block = View::make('pages.lesson.text')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'report'){
                        $prev_block = View::make('pages.lesson.report')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'video'){
                        $prev_block = View::make('pages.lesson.video')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'file'){
                        $prev_block = View::make('pages.lesson.file')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'top_skills' && !Auth::guest()){
                        $prev_block = View::make('pages.lesson.top_skills')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type=='dynamic' && !Auth::guest()){
                        $prev_block =  View::make('pages.lesson.dynamic')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type=='category' && !Auth::guest()){
                        $prev_block =  View::make('pages.lesson.category')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type=='sortable' && !Auth::guest() ){
                        //$page_has_scale = 'hidden';
                        $prev_block = View::make('pages.lesson.sortable')->withBlock($block);
                        echo $prev_block;
                        $page_has_submit = true;
                        $total_answers++;
                    }
                    else if($block->type == 'image_upload'){
                        $prev_block = View::make('pages.lesson.image_upload')->withBlock($block);
                        echo $prev_block;
                        $total_answers++;
                    }
                    else if($block->type == 'file_upload'){
                        $prev_block = View::make('pages.lesson.file_upload')->withBlock($block);
                        echo $prev_block;
                        $total_answers++;
                    }
                    else if($block->type == 'answer'){
                        $page_has_submit = true;
                        if(!Auth::guest()){
                            $answer = Block::find($block->answer_id);
                            if($answer!=null){
                                if($answer->answer_type=='Scale'){
                                    $prev_block = View::make('pages.lesson.previous-scale')->withBlock($block);
                                    echo $prev_block;
                                }
                                else if($answer->answer_type=='Skill Select'){
                                    $prev_block =  View::make('pages.lesson.previous-skill')->withBlock($block);
                                    echo $prev_block;
                                }
                                else{
                                    $prev_block = View::make('pages.lesson.answer')->withBlock($block);
                                    echo $prev_block;
                                }
                            }
                        }
                    }
                    else{
                        $page_has_submit = true;
                        $view = Str::slug($block->answer_type);
                        if($block->answer_type=='Scale') $page_has_scale = 'hidden';
                        if(!Auth::guest()){
                            if($block->answer_type=='Skill Select'){
                                if(trim($prev_block)!=''){
                                    $prev_block = View::make("pages.lesson.$view")->withBlock($block)->withSkills($skills);
                                    echo $prev_block;
                                    $total_answers++;
                                }
                            }
                            else{
                                $prev_block = View::make("pages.lesson.$view")->withBlock($block)->withSkills($skills);
                                echo $prev_block;
                                $total_answers++;
                            }
                        }
                    }
                }?>
                <div class='text-center top-pagination'></div>
                <?php
                // display blocks that are included in sections
                $section_count = 0;
                $section_open = false;
                $i = 0;
                $total_sections = 0;
                $created_sections = array();
                foreach($lesson->blocks()->where('in_section',1)->orderBy('ord','ASC')->get() as $block){
                    ++$i;
                    if($section_count==0 && !in_array($total_sections, $created_sections)){
                        $created_sections[] = $total_sections;
                        if($i==1) echo "<div data-section='$total_sections' class='blocks_section section-$total_sections'>";
                        else echo "<div data-section='$total_sections' class='blocks_section hidden_section section-$total_sections'>";
                        $section_open = true;
                    }
                    if($block->type == 'text'){
                        $prev_block = View::make('pages.lesson.text')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'report'){
                        $prev_block = View::make('pages.lesson.report')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'video'){
                        $prev_block = View::make('pages.lesson.video')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'file'){
                        $prev_block = View::make('pages.lesson.file')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type == 'top_skills' && !Auth::guest()){
                        $prev_block =  View::make('pages.lesson.top_skills')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type=='dynamic' && !Auth::guest()){
                        $prev_block = View::make('pages.lesson.dynamic')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type=='category' && !Auth::guest()){
                        $prev_block =  View::make('pages.lesson.category')->withBlock($block);
                        echo $prev_block;
                    }
                    else if($block->type=='sortable' && !Auth::guest()){
                        $page_has_submit = true;
                        //$page_has_scale = 'hidden';
                        $prev_block = View::make('pages.lesson.sortable')->withBlock($block);
                        echo $prev_block;
                        $total_answers++;
                    }
                    else if($block->type == 'answer' && !Auth::guest()){
                        $page_has_submit = true;
                        if(!Auth::guest()){
                            $answer = Block::find($block->answer_id);
                            if($answer!=null){
                                if($answer->answer_type=='Scale'){
                                    $prev_block = View::make('pages.lesson.previous-scale')->withBlock($block);
                                    echo $prev_block;
                                }
                                else if($answer->answer_type=='Skill Select'){
                                    $prev_block = View::make('pages.lesson.previous-skill')->withBlock($block);
                                    echo $prev_block;
                                }
                                else{
                                    $prev_block = View::make('pages.lesson.answer')->withBlock($block);
                                    echo $prev_block;
                                }
                            }
                        }
                    }
                    else if($block->type == 'image_upload'){
                        $prev_block = View::make('pages.lesson.image_upload')->withBlock($block);
                        echo $prev_block;
                        $total_answers++;
                    }
                    else if($block->type == 'file_upload'){
                        $prev_block = View::make('pages.lesson.file_upload')->withBlock($block);
                        echo $prev_block;
                        $total_answers++;
                    }
                    else{
                        $page_has_submit = true;
                        $view = Str::slug($block->answer_type);
                        if($block->answer_type=='Scale') $page_has_scale = 'hidden';
                        if(!Auth::guest()){
                            if($block->answer_type=='Skill Select'){
                                if(trim($prev_block)!=''){
                                    $prev_block = View::make("pages.lesson.$view")->withBlock($block)->withSkills($skills);
                                    echo $prev_block;
                                    $total_answers++;
                                }
                                else{
                                    $section_count-=2;
                                    if($section_count<-2) $section_count=-2;
                                }
                            }
                            else{
                                $prev_block = View::make("pages.lesson.$view")->withBlock($block)->withSkills($skills);
                                echo $prev_block;
                                $total_answers++;
                            }
                        }
                    }
                   if($section_count == $lesson->section_capacity - 1){
                       $total_sections++;
                       echo "</div>";
                        $section_open = false;
                        $section_count = -1;
                   } 
                   $section_count++;
                }
                if($section_open){
                     if($section_count>0) $total_sections++;
                    echo '</div>';
                }
                if($total_sections>1 && !Auth::guest()){
                    $page_has_scale = 'hidden';
                    echo "<div class='section_nav section_buttons'>
                            <button type='button' class='btn btn-primary page-btn' onclick='next_section_page(0, \"next\", $total_sections)'>Next Step</button>
                        </div>";
                    echo "<div class='text-center'><ul class='pagination'>";
                    
                    for($i=0;$i<$total_sections;++$i){
                        $active = '';
                        $j = $i+1;
                        if($i==0) $active = 'active';
                        echo "<li class='pag-$i $active'><span class='btn btn-link' onclick='quick_section($i, $total_sections)'>$j</span></li>";
                    }
                    echo "</ul></div><script>total_sections  = $total_sections;</script>";
                }
            ?>   
            <br style='clear:both' />
            <input type="hidden" name="lesson" value="{{$lesson->id}}" />
            @if(Auth::guest())
                You need to be registered and logged in to take this exam. <a href="{{url('login')}}">Log in</a>
            <!--@-elseif(admin())-->
            
            @elseif($page_has_submit)
            <?php $page_has_scale = '';?>
            <div class="text-center" style="margin-bottom: 15px;">
                @if(!$lesson->all_answered())
                    <button id="submit_btn" class="btn btn-lg submit-btns btn-success {{$page_has_scale}}">Submit</button>
                    <button type="button" id="edit_btn" btn-lg class="btn submit-btns btn-danger {{$page_has_scale}}" style="display:none" onclick="edit_answers()">Edit Answers</button>
                @else
                    <script>
                        needs_edit = true;
                    </script>
                    <button id="submit_btn" class="btn btn-lg submit-btns btn-success {{$page_has_scale}}" style="display:none">Submit</button>
                    <button type="button" id="edit_btn" class="btn  btn-lg submit-btns btn-danger {{$page_has_scale}}" onclick="edit_answers()">Edit Answers</button>
                @endif
            </div>
            <div class="text-center" >{{$next_lesson_btn}}</div>
            @else
            @endif
            
            @if($page_has_submit==false)
                <?php
                 $prev = previous_lesson($lesson);
                     if($prev!=''){
                         echo " <a class='btn btn-success' href='".URL('lesson/'.$prev)."'><i class='glyphicon glyphicon-backward'></i> Previous lesson</a>";
                     }
                     
                $next_lesson = next_lesson($lesson);
//                if($next_lesson!='' && !Auth::guest()) $next_lesson_btn =  "<div class='text-center next-lesson-btn'>
//                                <a class='btn btn-success' href='".URL('lesson/'.$next_lesson)."'>Next lesson <i class='glyphicon glyphicon-forward'></i></a>
//                            </div><br />";
                if($next_lesson!='' && !Auth::guest()) $next_lesson_btn =  "
                                <a class='btn btn-success pull-right' href='".URL('lesson/'.$next_lesson)."'>Next lesson <i class='glyphicon glyphicon-forward'></i></a>
                            ";
                    echo $next_lesson_btn;
                    ?>
            @endif
            </form>
            @if(admin() && time()=='this has to fail')
                @if($unattended>0)
                    <button id="next_item_btn" type='button' class='btn btn-danger pull-right' onclick='next_unattended_item()'><i class="glyphicon glyphicon-forward"></i> Next Unattended Item</button>             
                    @else 
                    <button style="display: none;" id="next_item_btn" type='button' class='btn btn-danger pull-right' onclick='next_unattended_item()'><i class="glyphicon glyphicon-forward"></i> Next Unattended Item</button>
                @endif
            @endif
            
            <div id="posted_remarks">
                @if($remarks->count()>0)
                <h2 class="section-title">Coach’s Remarks</h2>
                    {{ View::make('pages.lesson.remarks')->withRemarks($remarks) }}
                    @if(!admin())
                        <button class="btn btn-default remark-reply-btn" onclick="remark_reply({{$lesson->id}})">Reply</button>
                    @endif
                @endif
                
            </div>
            
             @if(admin() && Session::has('user_id'))
             
             
                 <div>
                        <h4 class="leave_remarks">Leave Remarks:</h4>
                        <input type='hidden' name="user" id="remark_user" value='{{Session::get('user_id')}}' />
                        <input type='hidden' name="lesson" id="remark_lesson" value='{{$lesson->id}}' />
                        <textarea id="remark" class="form-control white-textarea"></textarea><br />
                        <button id="post_remark_btn" type="button" class="btn btn-danger" onclick="post_coach_remarks('{{url('post_remark')}}')">Submit</button>
                        <br />
                        <br />
                </div>
             @if(admin() && Session::has('user_id'))
            
            
                <div class="admin-panel" id="block-1">
                                @if($unattended>0)
                                <div id="mark_lesson_btn" >
                                    <button title="Mark Whole Lesson As Attended" data-placement="top" data-toggle="tooltip" type='button' class='btn btn-success btn-sm do-tooltip' onclick='lesson_attended({{$lesson->id}},{{Session::get('user_id')}})'><i class="glyphicon glyphicon-ok"></i> Mark Whole Lesson As Attended</button>
                                </div>
                                @endif
    
                                @if($next_unattended!='' && $next_unattended != null)
                                        @if($next_unattended->chapter_ord < $lesson->chapter_ord)
                                            <a title="Go back to first unattended lesson"  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                            <i class="glyphicon glyphicon-fast-backward"></i>
                                            Back to first unattended lesson
                                            </a>
                                        @elseif($next_unattended->chapter_ord == $lesson->chapter_ord)
                                            @if($lesson->ord > $next_unattended->ord)
                                                <a title="Go back to first unattended lesson"  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                                <i class="glyphicon glyphicon-fast-backward"></i>
                                                Back to first unattended lesson
                                                </a>
                                            @else
                                                <a title="Go to Next unattended lesson "  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                                 <i class="glyphicon glyphicon-fast-forward"></i>
                                                 Next unattended lesson
                                                 </a>
                                            @endif
                                        @else 
                                            <a title="Go to Next unattended lesson "  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                            <i class="glyphicon glyphicon-fast-forward"></i>
                                            Next unattended lesson
                                            </a>
                                        @endif
                                @endif
                            
                </div>
            @endif
            
                <div id="user_box">
                    <?php $user = User::find(Session::get('user_id'));?>
                     @if($user->avatar=='')
                        <img class="discussion-thumb" src="http://placehold.it/80x80&text={{$user->username}}" />
                    @else
                        <img class="discussion-thumb" src="{{url('assets/img/avatars/'.$user->avatar)}}" />
                    @endif<br />
                <a href="{{url('userpage/'.Session::get('user_id'))}}"><?php
                    echo $user->username;
                ?></a>
                     @if($unattended>0)
                     
                        <br />    <button id="mark_user_btn" title="Mark User As Attended" data-placement="right" data-toggle="tooltip" type='button' class='btn btn-success btn-sm do-tooltip mark_user' onclick='user_attended({{Session::get('user_id')}})'><i class="glyphicon glyphicon-ok"></i> Mark As Attended</button>
                    @endif
                </div>
             @endif
        </div>
    
   
        <!-- /.container -->
    </div>
    <!-- /.section -->
    <div id='hidden_assets' style='display:none'>
          <div class="progress" style='clear:both; display:block; margin-top:10px'>
          <div class="progress-bar progress-bar-success progress-bar-striped indicator" 
               role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
          </div>
        </div>
    </div>

@stop