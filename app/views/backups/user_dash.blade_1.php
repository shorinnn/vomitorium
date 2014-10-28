@extends($layout)

@section('content')
<div class="section">
        <?php $program = Program::find(Session::get('program_id'));    ?>
        <div class="container">
            <!--
            @if($program->lessons->count() > 0)
            <p class="text-center">
                @if(Lesson::user_courses()=='' || Lesson::user_courses()->count()==0)
                    <a style="font-size:18px" class="btn btn-success btn-lg" href="{{Lesson::first()}}"> <i class="glyphicon glyphicon-flag"></i> Begin BrilliantU Career Coaching</a>
                @else
                You last stopped at...
                <span class="purple" style="display:block;padding-top:10px; font-weight: bold">
                        @if(Lesson::user_courses()->last()->chapter_id > 0)
                            {{Lesson::user_courses()->last()->chapter->title}} - 
                        @endif
                        {{Lesson::user_courses()->last()->title}}</span><br />
                <a style="font-size:18px; padding:25px !important" class="btn btn-success btn-lg" 
                   href='{{URL('lesson/'.Lesson::user_courses()->last()->slug)}}'> <i class="glyphicon glyphicon-play"></i>
                    Click here to resume</a><br />
                    
                @endif
            </p>
            @endif
            -->
                @if($program->lessons->count() > 0)
                <p class="text-center">
                    @if(Lesson::last()==null)
                        <a style="font-size:18px" class="btn btn-success btn-lg" href="{{Lesson::first()}}"> <i class="glyphicon glyphicon-flag"></i> Begin BrilliantU Career Coaching</a>
                    @else
                    You last stopped at...
                    <span class="purple" style="display:block;padding-top:10px; font-weight: bold">
                            @if(Lesson::last()->chapter_id > 0)
                                {{Lesson::last()->chapter->title}} - 
                            @endif
                            {{Lesson::last()->title}}</span><br />
                    <a style="font-size:18px; padding:25px !important" class="btn btn-success btn-lg" 
                       href='{{URL('lesson/'.Lesson::last()->slug)}}'> <i class="glyphicon glyphicon-play"></i>
                        Click here to resume</a><br />

                    @endif
                </p>
                @endif
            <h3>Notifications</h3>
            @if(count($notifications)==0)
                You have no new notifications<br /><br />
            @else
                You have {{count($notifications)}} new {{singplural(count($notifications),'notifications')}}.<br /><br />
                <table class='table table-striped table-bordered'>
                @foreach($notifications as $n)
                <tr><td style="width:200px;" class="text-center"> 
                        <span data-toggle="tooltip" class='do-tooltip' data-original-title='{{format_date($n->updated_at)}}'>
                       {{$n->updated_at->diffForHumans()}}
                    </span>
                    </td>
                       @if(get_class($n)=='Remark')
                           <td><span class="label label-info">Remark</span></td>
                            <td>
                            <a href='{{url('lesson/'.$n->lesson->slug)}}'>{{ $n->lesson->title }}</a></td>
                            <td>{{ Str::words($n->remark, 15) }}</td>
                       @else
                       <td><span class="label label-default">Comment</span></td>
                            <td>
                            <a href='{{url('lesson/'.$n->block_answer->block->lesson->slug.'#comment-'.$n->id)}}'>{{ $n->block_answer->block->lesson->title }} - 
                                {{ $n->block_answer->block->title }}</a></td>
                             <td>{{ Str::words($n->reply, 15) }}</td>
                       
                       @endif
                </tr>
                @endforeach
            </table>
            @endif
            
            <!-- courses-->
            @if($courses=='' || $courses->count()==0)
            <a href="{{Lesson::first()}}">Begin BrilliantU Career Coaching</a>
            @else
            
                <?php
                $chapter='';
                $last_lesson = Lesson::find(Auth::user()->last_lesson);
                if($last_lesson==null){
                    $last_lesson = new Lesson();
                    $last_lesson->ord = $last_lesson->chapter_ord = 0;
                }
                ?>
                @foreach($courses as $lesson)
              <?php
              if($chapter!=$lesson->chapter_id){
                    $last_ord = DB::table('lessons')->where('chapter_id', $lesson->chapter_id)->max('ord');
                    $chapter = $lesson->chapter_id;
                    ?>
                    @if($lesson->chapter_id > 0) 
                        </table>
                    @endif
                    <table class="table table-striped course-table">
                    @if($lesson->chapter_id > 0 )
                        <tr><td><h4>{{$lesson->chapter->title or ''}}</h4></td></tr>
                    @endif
                    <?php  
              } ?>
                <tr>
                    <td>
                        @if($lesson->chapter_ord < $last_lesson->chapter_ord)
                            @if(!in_array($lesson->id, $visited))
                                <span class="label label-danger">New!</span>
                            @endif
                            <a href="{{URL('lesson/'.$lesson->slug)}}">{{$lesson->title}}</a>

                        @elseif($lesson->chapter_ord == $last_lesson->chapter_ord && $lesson->ord <= $last_lesson->ord)
                            @if(!in_array($lesson->id, $visited))
                                <span class="label label-danger">New!</span>
                            @endif
                            <a href="{{URL('lesson/'.$lesson->slug)}}">{{$lesson->title}}</a>
                        @else
                            {{$lesson->title}}
                        @endif
                        
                        
                    </td>
                </tr>
                @endforeach
                
                
                
            </table>
            @endif
            <!-- /courses-->
            
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop