            @foreach($data['chapters'] as $c)
            <div class="chapter-holder chapter-{{$c->id}}" id="chapter-holder-{{$c->id}}" data-id="{{$c->id}}">
                <div class='chapter chapter-{{$c->id}}'>
                    <div class='btn-cell'>
                        <button class='btn btn-default inline add-lesson-btn do-tooltip' data-id='{{$c->id}}'  
                            data-url="{{action("ModulesController@store_lesson")}}" title='Add Lesson'>
                             Add Lesson</button>
                        <button class='btn btn-danger btn-warning delete-btn do-tooltip'  title='Delete this module and associated lessons'
                            data-target='chapter' data-id='{{$c->id}}' data-url="{{route("chapters.destroy", $c->id)}}">
                            <i class='glyphicon glyphicon-trash'></i></button>
                    </div>
                    <img src='{{url('assets/img/module-icon.png')}}' class='module-icon do-tooltip' title="Module" /> 
                        <a class="editable editable-click do-tooltip" href="#" id="title"
                           data-type="text" data-pk="{{$c->id}}" data-name="title" 
                           data-url="{{url('chapters/update')}}"  title="{{$c->title}}" data-mode="inline">{{Str::limit($c->title, 80)}}</a>

                </div>
                   <ul class="sortable-lessons">
                       {{View::make('modules.lessons')->withC($c)->withLessons($c->lessons()->orderBy('ord','ASC')->get())}}
                   </ul>
            </div>
            @endforeach 