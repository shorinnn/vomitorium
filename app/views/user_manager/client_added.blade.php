<div class="text-center">
    <img src='{{url('assets/img/greensuccess.png')}}' />
    <br />
    <h3 class='green'>Success!</h3>
    <span class='green'>Client {{$user->first_name}} {{$user->last_name}} has been added.</span>
    <br />
    <br />
    <span class='green'>Username:</span> {{$user->username}}<br />
    <span class='green'>Password:</span> {{$password}}
    <br />
    <br />
    <button class='btn btn-default' onclick="add_client_modal(0,'#register_manual')">Add Another Client</button>
</div>