@extends($layout)

@section('content')
<div class="section">


        <div class="container">
            <!--Welcome {{Auth::user()->username}},-->
            <p class="text-center">
                @if(Lesson::user_courses()=='' || Lesson::user_courses()->count()==0)
                    <a style="font-size:18px" class="btn btn-success btn-lg" href="{{Lesson::first()}}"> <i class="glyphicon glyphicon-flag"></i> Begin BrilliantU Career Coaching</a>
                @else
                <a style="font-size:18px; padding:25px !important" class="btn btn-success btn-lg" href='{{URL('lesson/'.Lesson::user_courses()->last()->slug)}}'> <i class="glyphicon glyphicon-repeat"></i>
                    Continue where you last stopped</a><br />
                    <span class="purple" style="display:block;padding-top:10px; font-weight: bold">
                        @if(Lesson::user_courses()->last()->chapter_id > 0)
                            {{Lesson::user_courses()->last()->chapter->title}} - 
                        @endif
                        {{Lesson::user_courses()->last()->title}}</span><br />
                @endif
            </p>
            
            <h3>Coach Remarks</h3>
            @if($remarks->count()==0 || $comments->count()==0)
            You have no new coach remarks.
            @else
            You have {{$remarks->count()}} new coach {{singplural($remarks->count(),'remarks')}}.
            <table class='table table-striped table-bordered'>
                @foreach($remarks as $c)
                <tr><td style="width:200px;" class="text-center"> 
                        <!--{{format_date($c->updated_at)}}, {{$c->updated_at->diffForHumans()}}-->
                        <span data-toggle="tooltip" class='do-tooltip' data-original-title='{{format_date($c->updated_at)}}'>
                       {{$c->updated_at->diffForHumans()}}
                    </span>
                    </td>
                    <td>
                        <a href='{{url('lesson/'.$c->lesson->slug)}}'>{{ $c->lesson->title }}</a></td>
                    <td>{{ Str::words($c->remark, 15) }}</td>
                </tr>
                @endforeach
            </table>
            @endif
            <br />
            <br />

            <h3>Coach Comments</h3>
            @if($comments->count()==0)
            You have no new coach comments.
            @else
            You have {{$comments->count()}} new coach {{singplural($comments->count(),'comments')}}.
            <table class='table table-striped table-bordered' style="margin-top: 20px">
                @foreach($comments as $c)
                <tr><td style="width:200px;" class="text-center"> 
                        
                    
                    <span data-toggle="tooltip" class='do-tooltip' data-original-title='{{format_date($c->updated_at)}}'>
                       {{$c->updated_at->diffForHumans()}}
                    </span></td>
                    <td>
                        <a href='{{url('lesson/'.$c->block_answer->block->lesson->slug.'#comment-'.$c->id)}}'>{{ $c->block_answer->block->lesson->title }} - 
                            {{ $c->block_answer->block->title }}</a></td>
                    <td>{{ Str::words($c->reply, 15) }}</td>
                </tr>
                @endforeach
            </table>
            @endif
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop