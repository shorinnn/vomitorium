@extends($layout)
@section('content')
 <!-- Page Content -->

    <div class="section">
        <div class="container">
            <h1>Profile</h1>
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