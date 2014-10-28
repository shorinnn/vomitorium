<div class="table-responsive">
     @if(isset($users))
    <table class="table table-bordered table-striped">
    <thead>
        <tr><th>User</th><th>Type</th><th>Email</th><th>Active</th><th>Actions</th></tr>
    </thead>
    <tbody>
       
            @foreach($users as $user)
            <tr class="list-row list-row-{{$user->id}}">
            <td>
                {{$user->username}}
            </td>
            <td>
                    @foreach($user->roles as $role)
                    {{$role->name}}
                    @endforeach
            </td>
            <td>
                {{$user->email}}
            </td>
            <td>
                {{$user->confirmed}}
            </td>
            <td>
                <a type="button" href='{{url('userpage/'.$user->id)}}' class='btn btn-primary' onclick='show_busy()'>View User Page</a>
            </td></tr>
            @endforeach
    </tbody>
</table>
      @endif
</div>