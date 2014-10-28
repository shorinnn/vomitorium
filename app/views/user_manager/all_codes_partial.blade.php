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
            <td>{{$c->code}}</td>
            <td>{{$c->program->name}}</td>
            <td>{{format_date($c->created_at)}}</td>
            <td>
                @if($c->used_by > 0)
                {{format_date($c->used_at)}}
                @endif
            </td>
            <td>
                @if($c->used_by > 0)
                {{$c->user()->username}}
                @endif</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{$codes->links()}}
