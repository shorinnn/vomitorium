@extends($layout)

@section('content')
<div class="section">
        <?php $program = Program::find(Session::get('program_id')); ?>
    @if($program!=null)
        <div class="container">           
            <h3>Notifications</h3>
            @if(count($notifications)==0)
                You have no new notifications<br /><br />
            @else
            <a href="{{url('inbox/filter:unread')}}" style="color:red"><i class='glyphicon glyphicon-info-sign'></i> You have {{count($notifications)}} new {{singplural(count($notifications),'notifications')}}</a>
                <br /><br />
            @endif
            
            @if($program->lessons->count() > 0)
            <p class="text-center">
                @if(Lesson::last()==null)
                    <a style="font-size:18px" class="btn btn-success btn-lg" href="{{Lesson::first()}}"> <i class="glyphicon glyphicon-flag"></i> 
                        Begin {{$program->name}}</a>
                @else
                <strong>You last stopped at...</strong>
                <span class="purple" style="display:block;padding-top:10px; font-weight: bold">
                        @if(Lesson::last()->chapter_id > 0)
                            {{Lesson::last()->chapter->title}} - 
                        @endif
                        {{Lesson::last()->title}}</span><br />
                <a style="font-size:18px; padding:25px !important" class="btn btn-success btn-lg" 
                   href='{{URL('lesson/'.Lesson::last()->slug)}}'> <i class="glyphicon glyphicon-play"></i>
                    Click here to resume</a><br /><br />

                @endif
            </p>
            @endif
                
            @if(Auth::user()->coach(Session::get('program_id'))!=false)
                Your Coach: {{Auth::user()->coach(Session::get('program_id'))->username}}
            @endif
            
            <!-- courses-->
            @if($courses=='' || $courses->count()==0)
            <!--<a href="{{Lesson::first()}}">Begin BrilliantU Career Coaching</a>-->
            @else
            
                <?php
                $chapter='';
                $last_lesson = Lesson::last();
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
                        <tr class='chapter-row'><td><h4>{{$lesson->chapter->title or ''}}</h4></td></tr>
                    @endif
                    <?php  
              } ?>
                     @if(Lesson::last()!=null && Lesson::last()->id == $lesson->id)
                <tr class="lesson-glow lesson-row">
                    @else
                <tr class='lesson-row'>
                    @endif
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
                            @if(Lesson::last()->id == $lesson->id)
                            <span class="btn btn-success btn-xs no-btn">You are here</span>
                            @endif
                        @else
                            <span class='unavailable'>{{$lesson->title}}</span>
                        @endif
                        
                        
                    </td>
                </tr>
                @endforeach
                
                
                
            </table>
            @endif
            <!-- /courses-->
            
        </div>
        <!-- /.container -->
    @else
    <?php
    $processor = PaymentProcessor::where('name','Stripe')->first();
    $name = sys_settings('title')=='' ? sys_settings('domain') : sys_settings('title');
    ?>
        It appears that your subscriptions have expired. Please select your plan: <br />
        
        @foreach($plans as $p)
            <div class='well col-lg-5'>
            {{$p->name}}
            ${{$p->cost}}
            @if($p->type=='subscription')
            for {{$p->subscription_duration}} 
            {{singplural($p->subscription_duration, $p->subscription_duration_unit )}}
            @endif
            <br />
            <br />
                {{View::make('payment_plans.stripe_code')->withPlan($p)->withName($name)->withProcessor($processor)->render()}}
            </div>
        <div class='col-lg-1'></div>
        @endforeach
    @endif

    </div>
    <!-- /.section -->
    
@stop