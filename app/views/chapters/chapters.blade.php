<div id="ajax-content">
    <br />
    @if ($data['chapters']->count() == 0)
       <br /> No chapters available
    @else 
            {{$data['chapters']->links()}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <thead>
                <tr><th>Chapter</th><th>Published</th><th>Order</th><th>Created at</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($data['chapters'] as $chapter)
                <tr class="list-row list-row-{{$chapter->id}}">
                <td>
                    <a class="editable" href="#" id="title" data-type="text" data-pk="{{$chapter->id}}" 
                       data-name="title" data-url="{{url('chapters/update')}}" data-original-title="Enter Title" data-mode='inline'>{{$chapter->title}}
                    </a>
                    <span data-toggle='tooltip' class="do-tooltip badge pull-right" title='{{$chapter->lessons->count()}} {{ singplural($chapter->lessons->count(),'Lessons')}}'>{{$chapter->lessons->count()}}</span>
                </td>
                <td>
                     <a class="editable" href="#" id="published" data-type="select" data-pk="{{$chapter->id}}" data-source='{"0":"No", "1":"Yes"}' 
                           data-value="{{$chapter->published}}" data-name="published" data-url="{{url('chapters/update')}}"
                           data-original-title="Enter Published" data-mode='inline'>{{yes_no($chapter->published)}}
                        </a>
                    </td>
                <td>{{$chapter->ord}}</td>
                <td>{{$chapter->created_at}}</td>
                <td>
                    <button data-toggle='tooltip' title='Move chapter up'  type="button" autocomplete="off" class='do-tooltip btn btn-primary{{move_up_class($chapter->ord)}}' onclick='move_item("{{url("chapters/move/up/$chapter->id")}}")'><i class="glyphicon glyphicon-arrow-up"></i></button>
                    <button data-toggle='tooltip' title='Move chapter down'  type="button" autocomplete="off" class='do-tooltip btn btn-primary{{move_down_class($chapter->ord, $data['last_ord'])}}' onclick='move_item("{{url("chapters/move/down/$chapter->id")}}")'><i class="glyphicon glyphicon-arrow-down"></i></button>
                    <!--{{link_to_action('LessonsController@index','Manage Lessons',array($chapter->id), array('class' => 'btn btn-primary', 'onclick'=>'show_busy()')) }}-->
                    <button data-toggle='tooltip' title='Delete chapter'  type="button" autocomplete="off" class='do-tooltip btn btn-danger' onclick='del({{$chapter->id}},"{{route("chapters.destroy", $chapter->id)}}")'><i class='glyphicon glyphicon-trash'></i></button>

                </td></tr>
                @endforeach
            </tbody>
        </table>
        </div>
            {{$data['chapters']->links()}}
    @endif
</div>