<div id="ajax-content">
    <br />
    @if ($data['lessons']->count() == 0)
       
       <div class='text-center'>
           You haven't created a lesson yet!<br /><br />
           <button type="button" class="btn btn-lg btn-success create-form-btn" onclick="create_form()">Create First Lesson</button>
       </div>
    @else 
    <button type="button" class="btn btn-default create-form-btn" onclick="create_form()">Add New</button>
    <br />
    {{$data['lessons']->links()}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <thead>
                <tr><th>Title</th><th>Chapter</th><th>URL</th><th>Created At</th><th style="width:250px">Actions</th></tr>
            </thead>
            <tbody>
                <?php 
                    $chapter = -1;
                    
                ?>
                @foreach($data['lessons'] as $lesson)
                
                <?php
                    if($chapter!=$lesson->chapter_id){
                        $last_ord = DB::table('lessons')->where('chapter_id', $lesson->chapter_id)->max('ord');
                        $chapter = $lesson->chapter_id;
                        ?>
                <tr><td colspan='6'><h4>{{$lesson->chapter->title or 'No Module'}}</h4></td></tr>
                        <?php
                    }
                    
                ?>
                <tr class="list-row list-row-{{$lesson->id}}">
                    <td>
                    <a class="editable" href="#" id="title" data-type="text" data-pk="{{$lesson->id}}" 
                       data-name="title" data-url="{{action("LessonsController@update", array($lesson->id))}}" data-original-title="Enter Title" data-mode='inline'>{{$lesson->title}}
                    </a>
                    </td>
                    <td>
                        <a class="editable" href="#" id="chapter_id" data-type="select" data-pk="{{$lesson->id}}" data-source='{{$data['chapters']}}' 
                           data-value="_{{$lesson->chapter_id}}" data-name="chapter_id" data-url="{{action("LessonsController@update", array($lesson->id))}}"
                           data-original-title="Enter Chapter" data-mode='inline'>{{$lesson->chapter->title or 'None'}}
                        </a>
                    </td>
                    <td>
                    <a class="editable" href="#" id="slug" data-type="text" data-pk="{{$lesson->id}}" 
                       data-name="slug" data-url="{{action("LessonsController@update", array($lesson->id))}}" data-original-title="Enter URL" data-mode='inline'>{{$lesson->slug}}
                    </a>
                    </td>
                   <!-- <td>{{$lesson->ord}}</td>-->
                    <td>{{$lesson->created_at}}</td>
                    <td class='text-right'>
                        <button data-toggle="tooltip" title='Move lesson up' type="button" autocomplete="off" class='do-tooltip btn btn-primary{{move_up_class($lesson->ord)}}' onclick='move_item("{{action("LessonsController@move", array($lesson->id,'up'))}}")'><i class="glyphicon glyphicon-arrow-up"></i></button>
                        <button data-toggle="tooltip" title='Move lesson down' type="button" autocomplete="off" class='do-tooltip btn btn-primary{{move_down_class($lesson->ord, $last_ord)}}' onclick='move_item("{{action("LessonsController@move", array($lesson->id,'down'))}}")'><i class="glyphicon glyphicon-arrow-down"></i></button>
                        <a data-toggle="tooltip" title='Lesson editor' href="{{url("lessons/$lesson->id/editor")}}"  class='btn btn-primary do-tooltip' onclick="show_busy()"><i class='glyphicon glyphicon-pencil'></i></a>
                        @if($lesson->published=='1')
                            <a  data-toggle="tooltip" title='Public view' href="{{url("lesson/$lesson->slug")}}"  target="_blank" class='do-tooltip btn btn-primary'><i class='glyphicon glyphicon-eye-open'></i></a>
                        @endif
                        <button data-toggle="tooltip" title='Delete lesson' type="button" autocomplete="off" class='btn btn-danger do-tooltip' onclick='del({{$lesson->id}},"{{action("LessonsController@destroy", array($lesson->id))}}")'><i class="glyphicon glyphicon-trash"></i></button>
                    
                    </td>
               </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    {{$data['lessons']->links()}}
    @endif
</div>