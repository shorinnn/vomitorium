@extends($layout)

@section('content')
<div class="section lesson-content">
<?php
    $visited = Auth::user()->lessons;
    if($visited=='') $visited = array();
    else $visited = json_decode($visited, true)
?>

        <div class="container">
            <h2 class="section-title">
                @if(Session::has('program_id'))
                    {{Program::find(Session::get('program_id'))->name}}
                @endif
            </h2>
            
            <!-- courses-->
            @if($courses=='' || $courses->count()==0)
            <a href="{{Lesson::first()}}">Begin Program</a>
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
                 @if(Lesson::last() !=null && Lesson::last()->id == $lesson->id)
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
        <div style='height:200px;'></div>
    </div>
    <!-- /.section -->
    
@stop