@extends($layout)

@section('content')
<div class="section">
    <p class='alert alert-info'>Note: Admin Users are no longer displayed</p>
        <div class="container">
            <button class='btn btn-default' onclick="add_client_modal(1)"><i class='glyphicon glyphicon-plus'></i> Add Client</button>
           
            <br />
            <input type="text" name="search" id="search" placeholder="Search clients"  class="form-control input-lg" autocomplete="off"
                   data-url='{{url('users/search')}}' />
            {{View::make('user_manager.users')->withUsers($users)->withRoles($roles)}}

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop