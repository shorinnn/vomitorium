@extends($layout)

@section('content')
<div class="section">


        <div class="container">
            @if(admin() && Session::has('user_id'))
            <div id='coach_remarks'>
                    <h4>Coach Remarks:</h4>
                    <input type='hidden' name="user" id="remark_user" value='{{Session::get('user_id')}}' />
                    <input type='hidden' name="lesson" id="remark_lesson" value='{{$lesson->id}}' />
                    <textarea id="remark" class="form-control white-textarea">{{$current_remark}}</textarea><br />
                    <button type="button" class="btn btn-primary" onclick="cancel_coach_remarks()">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="post_coach_remarks('{{url('post_remark')}}')">Submit</button>
                    <br />
                    <br />
            </div>
            
                <div class="panel panel-default block admin-panel" id="block-1">
                      <div class="panel-heading">
                              <i class="glyphicon glyphicon-cog"></i> Admin Panel 
                              <button class='btn btn-primary btn-xs' onclick='toggle_block("block-1")'><i class="toggle-block-btn glyphicon glyphicon-resize-small"></i></button> 
                      </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                @if($current_user->avatar=='')
                                    <img class="admin-user-thumb2" src="http://placehold.it/80x80&text={{$current_user->username}}" />
                                @else
                                    <img class="admin-user-thumb2" src="{{url('assets/img/avatars/'.$current_user->avatar)}}" />
                                @endif
                                <br />
                                <a href='{{url("userpage/".Session::get('user_id'))}}'>{{$current_user->username}}</a>
                            </div>
                            <div class="col-md-6 admin-panel-btns">
           
                                @if($unattended>0)
                                    <button id="mark_lesson_btn" title="Mark Lesson as Attended" data-placement="right" data-toggle="tooltip" type='button' class='btn btn-success btn-sm do-tooltip' onclick='lesson_attended({{$lesson->id}},{{Session::get('user_id')}})'><i class="glyphicon glyphicon-ok"></i></button><br />
                                @endif

                                <button type='button' title=" Leave coach remarks"  data-placement="right" data-toggle="tooltip" onclick="show_coach_remarks()" class='btn btn-danger btn-sm do-tooltip'><i class="glyphicon glyphicon-pencil"></i></button><br />
                                
                                <!-- @if($unattended>0)
                                <button data-placement="right" data-toggle="tooltip" title="Next unattended item on this page" id="next_item_btn" type='button' class='btn btn-warning btn-sm do-tooltip' onclick='next_unattended_item()'><i class="glyphicon glyphicon-search"></i></button>    <br />         
                                @else 
                                <button data-placement="right" data-toggle="tooltip" title="Next unattended item on this page" style="display: none;" id="next_item_btn" type='button' class='btn btn-warning btn-sm do-tooltip' onclick='next_unattended_item()'><i class="glyphicon glyphicon-search"></i></button><br />
                                @endif-->
                            
                                @if($next_unattended!='')
                                        @if($next_unattended->chapter_ord < $lesson->chapter_ord)
                                            <a title="Go back to first unattended lesson"  data-placement="right" data-toggle="tooltip" class='btn btn-primary btn-sm do-tooltip' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                            <i class="glyphicon glyphicon-fast-backward"></i>
                                            </a>
                                        @elseif($next_unattended->chapter_ord == $lesson->chapter_ord)
                                            @if($lesson->ord > $next_unattended->ord)
                                                <a title="Go back to first unattended lesson"  data-placement="right" data-toggle="tooltip" class='btn btn-primary btn-sm do-tooltip' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                                <i class="glyphicon glyphicon-fast-backward"></i>
                                                </a>
                                            @else
                                                <a title=" Next unattended lesson "  data-placement="right" data-toggle="tooltip" class='btn btn-primary btn-sm do-tooltip' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                                 <i class="glyphicon glyphicon-fast-forward"></i>
                                                 </a>
                                            @endif
                                        @else 
                                            <a title=" Next unattended lesson "  data-placement="right" data-toggle="tooltip" class='btn btn-primary btn-sm do-tooltip' href='{{url("lesson/$next_unattended->slug/".Session::get('user_id'))}}'>
                                            <i class="glyphicon glyphicon-fast-forward"></i>
                                            </a>
                                        @endif
                                @endif
                            
                            </div>
                            </div>
                </div>
            @endif

            
            @if($unread_remark && !admin())
            @endif 
            <div id="posted_remarks">
                @if($remarks->count()>0)
                <h2 class="section-title">Coach’s Remarks</h2>
                @endif
                {{ View::make('pages.lesson.remarks')->withRemarks($remarks) }}
            </div>
            <form id="lesson_form" action="" method="post">
            <h1 class="lesson-title">{{$lesson->title}}</h1>
                @if(Session::has('success'))
                    <p class='alert alert-success'>{{Session::get('success')}}</p>
                @endif
                @if(Session::has('error'))
                    <p class='alert alert-danger'>{{Session::get('error')}}</p>
                @endif
            <?php
                $page_has_scale = '';
                foreach($lesson->blocks()->orderBy('ord','ASC')->get() as $block){
                    if($block->type == 'text'){
                        echo View::make('pages.lesson.text')->withBlock($block);
                    }
                    else if($block->type == 'answer'){
                        if(!Auth::guest()){
                            $answer = Block::find($block->answer_id);
                            if($answer->answer_type=='Scale'){
                                echo View::make('pages.lesson.previous-scale')->withBlock($block);
                            }
                            else if($answer->answer_type=='Skill Select'){
                                echo View::make('pages.lesson.previous-skill')->withBlock($block);
                            }
                            else echo View::make('pages.lesson.answer')->withBlock($block);
                        }
                    }
                    else{
                        
                        $view = Str::slug($block->answer_type);
                        if($block->answer_type=='Scale') $page_has_scale = 'hidden';
                        if(!Auth::guest()) echo View::make("pages.lesson.$view")->withBlock($block)->withSkills($skills);
                    }
                }
            ?>   
            <br />
            <input type="hidden" name="lesson" value="{{$lesson->id}}" />
            @if(Auth::guest())
                You need to be registered and logged in to take this exam. <a href="{{url('login')}}">Log in</a>
            @elseif(admin())
            
            @else
                @if(!$lesson->all_answered())
                    <button id="submit_btn" class="btn btn-primary {{$page_has_scale}}">Submit</button>
                    <button type="button" id="edit_btn" class="btn btn-danger {{$page_has_scale}}" style="display:none" onclick="edit_answers()">Edit Answers</button>
                @else
                    <button id="submit_btn" class="btn btn-primary {{$page_has_scale}}" style="display:none">Submit</button>
                    <button type="button" id="edit_btn" class="btn btn-danger {{$page_has_scale}}" onclick="edit_answers()">Edit Answers</button>
                @endif
            @endif
            </form>
            @if(admin() && time()=='this has to fail')
                @if($unattended>0)
                    <button id="next_item_btn" type='button' class='btn btn-danger pull-right' onclick='next_unattended_item()'><i class="glyphicon glyphicon-forward"></i> Next Unattended Item</button>             
                    @else 
                    <button style="display: none;" id="next_item_btn" type='button' class='btn btn-danger pull-right' onclick='next_unattended_item()'><i class="glyphicon glyphicon-forward"></i> Next Unattended Item</button>
                @endif
            @endif
        </div>
        <!-- /.container -->
        <div style='height:200px;'></div>
    </div>
    <!-- /.section -->
    
@stop