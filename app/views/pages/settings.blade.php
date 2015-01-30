@extends($layout)
@section('content')
 <!-- Page Content -->

    <div class="section">
        <div class="container">
             @if(Session::has('success'))
                    <p class='alert alert-success text-center'>{{Session::get('success')}}</p>
             @endif
             @if(Session::has('error'))
                    <p class='alert alert-danger alert-warning text-center'>{{Session::get('error')}}</p>
             @endif
            <h1>Profile</h1>
            Password:
            <form method="post" action="{{ action('UserController@change_password') }}">
                <div class="form-group">
                    <input type="password" name="old" class="form-control" placeholder="Old Password" />
                </div>
                <div class="form-group">
                    <input type="password" name="new" class="form-control" placeholder="New Password" />
                </div>
                <div class="form-group">
                    <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" />
                </div>
                <button class="btn btn-default">Change Password</button>
            </form>
            Your avatar:<br />
            @if(Auth::user()->avatar=='')
             <img class="discussion-thumb" src="http://placehold.it/80x80&text={{Auth::user()->username}}" />
            @else
               <img class="discussion-thumb" src="{{url('assets/img/avatars/'.Auth::user()->avatar)}}" />
            @endif
            <br />
            Upload new: <input type='file' name='file' id='file' />
            <button class='btn btn-default' onclick='upload_avatar()'>Upload</button>
        </div>
    </div>
@stop