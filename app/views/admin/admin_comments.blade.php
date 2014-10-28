@extends($layout)

@section('content')
<div class="section">


        <div class="container">
            <h1>Welcome {{Auth::user()->username}}</h1>
            <h3>{{$pageTitle}}</h3>
            <div id="ajax-content">
                {{ View::make('admin.admin_comments_partial')
                          ->with('pageTitle',$pageTitle)->withComments($comments)}}
            </div>
       </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop