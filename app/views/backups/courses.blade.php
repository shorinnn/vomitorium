@extends($layout)

@section('content')
<div class="section lesson-content">
<?php
    $visited = Auth::user()->lessons;
    if($visited=='') $visited = array();
    else $visited = json_decode($visited, true)
?>

        <div class="container">
            <h2 class="section-title">BrilliantU Career Coaching Program</h2>
            
            @if($courses=='' || $courses->count()==0)
            <a href="{{Lesson::first()}}">Begin BrilliantU Career Coaching</a>
            @else
            
                <?php
                $chapter='';?>
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
                <tr><td><a href="{{URL('lesson/'.$lesson->slug)}}">{{$lesson->title}}</a>
                        @if(!in_array($lesson->id, $visited))
                        <span class='new'>NEW!</span>
                        @endif
                    </td></tr>
                @endforeach
                
                
                
            </table>
            @endif
        </div>
    
   
        <!-- /.container -->
        <div style='height:200px;'></div>
    </div>
    <!-- /.section -->
    
@stop