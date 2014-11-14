@foreach($lessons as $l)
<div class='chapter-{{$c->id}} lesson lesson-{{$l->id}} pull-right module-lesson' data-id="{{$l->id}}">
    <!--<i class="glyphicon glyphicon-align-justify"></i>-->
    <img src='{{url('assets/img/lesson-icon.png')}}' class='lesson-icon do-tooltip' title='Lesson' /> 
        <div class='btn-cell'>
             <a 
                 @if($l->published==1)
                     href="{{url("lessons/view_lesson/".$l->id)}}" 
                     title="Preview"
                 @else
                    title="Unpublished"
                 @endif
                 target="_blank" class='btn btn-default inline preview-lesson do-tooltip
                @if($l->published!=1)
                    disabled-preview-lesson
                @endif'>
                 <i class="glyphicon glyphicon-eye-open"></i></a>

            <a href="{{url("lessons/$l->id/editor")}}" class='btn btn-default inline do-tooltip' title='Edit lesson'>
                Edit</a>
            <button class='btn btn-danger btn-warning delete-btn do-tooltip' title='Delete this lesson' 
                    data-target='lesson' data-id='{{$l->id}}' data-url="{{route("lessons.destroy", $l->id)}}">
                <i class='glyphicon glyphicon-trash'></i></button>
        </div>

        
        <a class="editable do-tooltip" href="#" id="title" data-type="text" data-pk="{{$l->id}}"  title='{{$l->title}}'
           data-name="title" data-url="{{action("LessonsController@update", array($l->id))}}" data-mode='inline'>
            {{Str::limit($l->title,55)}}</a>
    </div>
@endforeach