<table>
    <tr><td>First Name </td><td>
            <a id="first_name" class='editable' data-type="text" data-pk="{{$admin->id}}" data-name="first_name" 
               data-url="{{url("accounts/admin/$account")}}" data-original-title="First Name" data-mode="inline">{{$admin->first_name}}</a>
        </td></tr>
    <tr><td>Last Name </td><td>
        <a id="last_name" class='editable' data-type="text" data-pk="{{$admin->id}}" data-name="last_name" 
               data-url="{{url("accounts/admin/$account")}}" data-original-title="Last Name" data-mode="inline">{{$admin->last_name}}</a>
        </td></tr>
    <tr><td>Email </td><td>
            <a id="email" class='editable' data-type="text" data-pk="{{$admin->id}}" data-name="email" 
               data-url="{{url("accounts/admin/$account")}}" data-original-title="First Name" data-mode="inline">{{$admin->email}}</a>
        </td></tr>
</table>