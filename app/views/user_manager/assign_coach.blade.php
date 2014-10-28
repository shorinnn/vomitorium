@extends($layout)

@section('content')
<form method="post" action="{{url('users/assign_coach/'.$user->id)}}" class="update_form">
<div class="section">

        <div class="container">
            @if(count($user->programs())<1)
            <div class="alert alert-danger alert-warning">
                Cannot assign a coach because this user is not associated with any programs yet.
            </div>
            @else
            <table class="table table-striped table-bordered">
                @foreach($user->programs() as $p)
                <tr><td>{{$p->name}} <input type="hidden" name="program[]" value="{{$p->id}}"</td>
                    <td>
                        @foreach($admins as $a)
                        <input type="checkbox" name="assigned_admin[{{$p->id}}][]" 
                               value="{{$a->id}}" id="a-{{$p->id}}-{{$a->id}}"
                               @if($user->is_assigned($relations, $p->id, $a->id))
                               checked = "checked"
                               @endif
                               />
                        <label for="a-{{$p->id}}-{{$a->id}}">{{$a->username}}</label><br />
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
            <button class="btn btn-default">Update</button>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
</form>
@stop