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
                <?php
                    $next_lesson = next_lesson($lesson);
                ?>
                     @if($lesson->progress() > 0)
                     <?php
                      
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
                
                @if($lesson_remarks->count() > 0)
                    <p class="green-bg conversations-title">Lesson Remarks</p>
                @else
                    <p class="green-bg conversations-title hidden">Lesson Remarks</p>
                @endif
                
                @if($total_lesson_remarks>1)
                    <button type='button' data-id="{{$current_user->id or 0}}" onclick="load_lesson_comments({{$lesson->id}},1)" class="btn btn-default load-lesson-comments">
                        <img src="http://chicken.imacoa.ch/assets/img/arrow-point.png" alt=""> Load Earlier Messages
                    </button><br />
                    @endif
                <div class="lesson-comments">
                    {{ View::make('pages.lesson.remarks')->withRemarks($lesson_remarks) }}
                </div>
                @if(!Auth::guest() && $lesson_remarks->count() > 0)
                    @if(!admin())
                        @if($lesson_remarks->count()>0)
                            @if($remarks[$remarks->count()-1]->posted_by == 'user')
                                <button type='button' class='btn btn-default force-edit-remark' onclick='force_edit(".lesson-comments")'>Edit</button>
                            @else
                            <button style='display:none' type='button' class='btn btn-default force-edit-remark' onclick='force_edit(".lesson-comments")'>Edit</button>
                            <div class='remark-post-area' style='display:none'>
                                <span id='remark-reply-area'></span>
                                <textarea id="remark_reply_top" class="white-textarea summernote_editor"></textarea>

                                <button type="button" class="btn btn-default2 message-send" 
                                        data-rte='#remark_reply_top' data-container='.lesson-comments' 
                                            onclick="do_remark_reply(event, {{$lesson->id}})">Send</button>
                                <ul class="list-unstyled option-box-2">
                                    <li><a href="#" data-toggle="tooltip" title="" data-input='attachment' 
                                           data-rte='#remark_reply_top' data-input='attachment'
                                           data-original-title="Attach" class="do-tooltip icon-2" onclick="attach(event)"></a></li>
                                    <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" data-target='#remark_reply_top' onclick="discard(event)" class="do-tooltip icon-3"></a></li>
                                </ul>
                            </div>
                            <br class='clearfix clear_fix' />
                            <button type='button' class='btn btn-default show-remark-reply' onclick='show_remark_reply()'>Reply</button>
                            @endif
                            <span class='clearfix clear_fix'></span>
                        @endif
                    @else
                        @if(Session::has('user_id'))
                            @if($remarks[$remarks->count()-1]->posted_by == 'admin')
                                <button type='button' class='btn btn-default' onclick='force_edit(".lesson-comments")'>Edit</button>
                            @else
                                <button style='display:none' type='button' class='btn btn-default force-edit-remark' onclick='force_edit(".lesson-comments")'>Edit</button>
                                    @if($lesson_remarks->count()>0)
                                        <div class='remark-post-area' style='display:none'>
                                    @else 
                                        <div class="message-col remark-post-area">
                                        <h2>
                                        Leave Remarks
                                        </h2>
                                    @endif
                                    
                                    <textarea id="top-remark" class="form-control white-textarea summernote_editor"></textarea>


                                    <button id="post_remark_btn" type="button" class="btn btn-default2 message-send" 
                                            data-rte='#top-remark' data-container='.lesson-comments'
                                            onclick="post_coach_remarks(event,'{{url('post_remark')}}')">Send</button>
                                    <ul class="list-unstyled option-box-2">
                                        <li><a href="#" data-toggle="tooltip" title="" data-original-title="Attach" data-input='attachment'
                                               data-rte='#top-remark'
                                               class="do-tooltip icon-2" onclick="attach(event)"></a></li>
                                        <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" class="do-tooltip icon-3" 
                                               data-target='#top-remark' onclick="discard(event)"></a></li>
                                    </ul>
                                    <br />
                                    <br />
                                </div>
                                        @if($lesson_remarks->count()>0)
                        <br class='clearfix clear_fix' />
                        <br class='clearfix clear_fix' />
                            <button type='button' class='btn btn-default show-remark-reply' onclick='show_remark_reply()'>Reply</button>
                        @endif
                        @endif
                        
                    @endif
                    <span class='clearfix clear_fix'></span>
                    @endif
                @endif
            <?php
            $page_has_submit = false;
            $prev_block = '';
            $first_in_section = $lesson->blocks()->where('in_section', 1)->orderBy('ord','asc')->get();
            if($first_in_section->count()==0) $first_in_section = 99999;
            else $first_in_section = $first_in_section->first()->ord;
                $page_has_scale = '';
                // display blocks that are not included in sections first
                foreach($lesson->blocks()->where('ord','<=', $first_in_section)->where('in_section',0)->orderBy('ord','ASC')->get() as $block){
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
            <!-- after section blocks -->
            <?php
            $last_in_section = $lesson->blocks()->where('in_section', 1)->orderBy('ord','desc')->get();
            if($last_in_section->count()==0) $last_in_section = 99999999;
            else $last_in_section = $last_in_section->first()->ord;
                // display blocks that are not included in sections first
                foreach($lesson->blocks()->where('ord','>=', $last_in_section)->where('in_section',0)->orderBy('ord','ASC')->get() as $block){
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
            <!--/ after section blocks -->
            <input type="hidden" name="lesson" value="{{$lesson->id}}" />
            @if(Auth::guest())
                You need to be registered and logged in to take this exam. <a href="{{url('login')}}">Log in</a>
            
            @elseif($page_has_submit)
            <?php $page_has_scale = '';?>

            <div class="text-center" style="margin-bottom: 15px;">
                @if(!$lesson->already_submitted())
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
            <br class='clearfix clear_fix' />
            </form>
           
            @if(admin() && $lesson_remarks->count()==0 && Session::has('user_id'))
            <div>
                <h2>
                    @if($lesson_remarks->count()>0)
                        Compose Your Message
                    @else
                        Leave Remarks
                    @endif
                </h2>
                <textarea id="top-remark" class="form-control white-textarea summernote_editor">  <p><br></p> </textarea>


                                    <button id="post_remark_btn" type="button" class="btn btn-default2 message-send" 
                                            data-rte='#top-remark' data-container='.lesson-comments'
                                            onclick="post_coach_remarks(event,'{{url('post_remark')}}')">Send</button>
                                    <ul class="list-unstyled option-box-2">
                                        <li><a href="#" data-toggle="tooltip" title="" data-original-title="Attach" data-input='attachment'
                                               data-rte='#top-remark'
                                               class="do-tooltip icon-2" onclick="attach(event)"></a></li>
                                        <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" class="do-tooltip icon-3" 
                                               data-target='#top-remark' onclick="discard(event)"></a></li>
                                    </ul>
                                    <br />
                                    <br />
                        </div><span class='clearfix clear_fix'></span>
            @endif
            <div id="posted_remarks_big">
                @if($remarks->count()>0 && (!admin()  || (admin() && Session::has('user_id')) ))
                <button class='btn btn-primary do-tooltip center-block' 
                            onclick='window.open("{{url("conversation/$lesson->id/".Session::get('user_id'))}}","_blank",
                            "location=0, menubar=0, statusbar=0, toolbar=0, titlebar=0, scrollbars=1, top=0, width=1000")' title='Open in a new window'><i class='glyphicon glyphicon-new-window'></i>
                Open Program Wide Conversation</button>
                <div class='remarks-container' style='display:none'>
                    <div id='posted_remarks'>
                        {{ View::make('pages.lesson.remarks')->withRemarks($remarks) }}
                    </div>
                    @if(!admin())
                        @if($remarks[$remarks->count()-1]->posted_by == 'user')
                            <button class='btn btn-default' onclick='force_edit("#posted_remarks")'>Edit</button>
                        @else
                            <span id='remark-reply-area'></span>
                            <textarea id="remark_reply" class="white-textarea summernote_editor"></textarea>

                            <button type="button" class="btn btn-default2 message-send"
                                    data-rte='#remark_reply' 
                                    data-container='#posted_remarks' 
                                    onclick="do_remark_reply(event, {{$lesson->id}})">Send</button>
                            <ul class="list-unstyled option-box-2">
                                <li><a href="#" data-toggle="tooltip" title="" data-input='attachment' data-original-title="Attach" 
                                       data-rte='#remark_reply' data-input='attachment'
                                       class="do-tooltip icon-2" onclick="attach(event)"></a></li>
                                <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" data-target='#remark_reply' onclick="discard(event)" class="do-tooltip icon-3"></a></li>
                            </ul>
                        @endif
                    @else
                        @if(Session::has('user_id'))
                            @if($remarks[$remarks->count()-1]->posted_by == 'admin')
                            <button class='btn btn-default' onclick='force_edit("#posted_remarks")'>Edit</button>
                            @else
                                <div class="message-col">
                                        <h2>
                                            @if($remarks->count()>0)
                                                Compose Your Message
                                            @else
                                                Leave Remarks
                                            @endif
                                        </h2>

                                        <span id='remark-reply-area'></span>
                                        <textarea id="remark" class="form-control white-textarea summernote_editor">

                                        </textarea>


                                        <button id="post_remark_btn" type="button" class="btn btn-default2 message-send" 
                                                data-rte='#remark' data-container='#posted_remarks'
                                                onclick="post_coach_remarks(event, '{{url('post_remark')}}')">Send</button>
                                        <ul class="list-unstyled option-box-2">
                                            <li><a href="#" data-toggle="tooltip" title="" data-original-title="Attach" 
                                                   data-rte='#remark'
                                                   data-input='attachment' class="do-tooltip icon-2" onclick="attach(event)"></a></li>
                                            <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" class="do-tooltip icon-3" data-target='#remark' onclick="discard(event)"></a></li>
                                        </ul>
                                        <br />
                                        <br />
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
                @endif
                
                
            </div>
            @if(Auth::check())
                {{ View::make('pages.lesson.group_lesson_comments')->withTotal_group_remarks($total_group_remarks)->withGroup_remarks($group_remarks)->withRemarks($remarks)->withLesson($lesson) }}
            @endif
                         
             @if(admin() && Session::has('user_id'))
            
            
                <div class="admin-panel" id="block-1">
                                @if($unattended>0)
                                <div id="mark_lesson_btn" >
                                    <button title="Mark Whole Lesson As Reviewed" data-placement="top" data-toggle="tooltip" type='button' class='btn btn-success btn-sm do-tooltip' onclick='lesson_attended({{$lesson->id}},{{Session::get('user_id')}})'><i class="glyphicon glyphicon-ok"></i> Mark Whole Lesson As Reviewed</button>
                                </div>
                                @endif
    
                                @if($next_unattended!='' && $next_unattended != null)
                                        @if($next_unattended->chapter_ord < $lesson->chapter_ord)
                                            <a title="Go back to first not yet reviewed lesson"  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                            <i class="glyphicon glyphicon-fast-backward"></i>
                                            Back to first not yet reviewed lesson
                                            </a>
                                        @elseif($next_unattended->chapter_ord == $lesson->chapter_ord)
                                            @if($lesson->ord > $next_unattended->ord)
                                                <a title="Go back to first not yet reviewed lesson"  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                                <i class="glyphicon glyphicon-fast-backward"></i>
                                                Back to first not yet reviewed lesson
                                                </a>
                                            @else
                                                <a title="Go to Next not yet reviewed lesson "  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                                 <i class="glyphicon glyphicon-fast-forward"></i>
                                                 Next not yet reviewed lesson
                                                 </a>
                                            @endif
                                        @else 
                                            <a title="Go to Next not yet reviewed lesson "  data-placement="left" data-toggle="tooltip" class='btn btn-danger btn-sm do-tooltip next-lesson' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                            <i class="glyphicon glyphicon-fast-forward"></i>
                                            Next not yet reviewed lesson
                                            </a>
                                        @endif
                                @endif
                            
                </div>
            
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
                     
                        <br />    <button id="mark_user_btn" title="Mark User As Reviewed" data-placement="right" data-toggle="tooltip" type='button' class='btn btn-success btn-sm do-tooltip mark_user' onclick='user_attended({{Session::get('user_id')}})'><i class="glyphicon glyphicon-ok"></i> Mark As Reviewed</button>
                    @endif
                </div>
             @endif
        </div>
    
   
        <!-- /.container -->
    </div>
    <!-- /.section -->
    <div id='hidden_assets' style='display:none'>
        <input type='hidden' name="user" id="remark_user" value='{{Session::get('user_id')}}' />
        <input type='hidden' name="lesson" id="remark_lesson" value='{{$lesson->id}}' />
        <input type="file" name="attachment" id="attachment" />
        <input type="file" name="comment_attachment" id="comment_attachment" />
        <input type="hidden" name="attachments" id="attachments" value="[]" />
        <input type="hidden" name="comment_attachments" id="comment_attachments" value="[]" />
          <div class="progress" style='clear:both; display:block; margin-top:10px'>
          <div class="progress-bar progress-bar-success progress-bar-striped indicator" 
               role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
          </div>
        </div>
    </div>
    <script>
        var do_enable_rte = true;
        var rte_config = 3;
        @if(admin())
            var enable_autosave = 0;
        @else
            var enable_autosave = 1;
            var lesson_name = "{{$lesson->slug}}";
        @endif
    </script>
@stop