<table class='table table-condensed table-bordered table-striped'>
    <thead>
        <tr>
            <th>Code</th>
            <th>Program</th>
            <th>Generated At</th>
            <th>Used At</th>
            <th>Used By</th>
        </tr>
    </thead>
    <tbody>
        @foreach($codes as $c)
        <tr>
            <td>
                 <input type='text'  class='form-control selectable-txt pull-left copy-source-{{$c->id}} copy-to'
               style="width:80%" value='{{$c->code}}' data-clipboard-text="{{url("register/accesspass/$c->code")}}" 
         data-id='{{$c->id}}'
                />
    <img src="{{url('assets/img/clipboard.png')}}" class='copy-to' data-clipboard-text="{{url("register/accesspass/$c->code")}}" 
         data-id='{{$c->id}}' height="32" />
            </td>
            <td>{{$c->program->name}}</td>
            <td>{{format_date($c->created_at)}}</td>
            <td>
                @if($c->used_by > 0)
                {{format_date($c->used_at)}}
                @endif
            </td>
            <td>
                @if($c->used_by > 0 && User::find($c->used_by)!=null)
                {{User::find($c->used_by)->username}}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{$codes->links()}}
