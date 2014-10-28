<tr class='list-row list-row-{{$program->id}}'><td>
        <a class="editable" href="#" id="name" data-type="text" data-pk="{{$program->id}}" 
           data-name="name" data-url="{{url('programs/update')}}" data-original-title="Enter program name" data-mode='inline'>
            {{$program->name}}
        </a>
    </td>
    <td>{{$program->users->count()}}</td>
    <td><button class='btn btn-danger' onclick='del({{$program->id}}, "{{url('programs/'.$program->id)}}")'>Delete</button></td>
</tr>