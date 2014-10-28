@extends($layout)

@section('content')
 
<div class="section">

        <div class="container">
            @unless (Auth::guest())
            <h2 class="text-center alert alert-success"><i class='glyphicon glyphicon-ok'></i> You are logged in now!</h2>
                @if (Auth::user()->hasRole('Admin') )
                <h3 class='text-center alert alert-info'>You're an admin even!</h3>
                @endif
            @endunless
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <h3><i class="fa fa-check-circle"></i> Signs that Your Current Job is the Right One for You</h3>
                    <p>Having the right job is essential for experiencing brilliant life. Are you wondering about your current job and whether it is the right one for you? Trying career change counseling..</p>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h3><i class="fa fa-pencil"></i> Loving Your Life By Loving Your Job</h3>
                    <p>We all know that our lives are better when we are doing the things we love the most. Unfortunately, many people end up finding themselves in a hard spot, because...</p>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h3><i class="fa fa-folder-open"></i> When to Consider a Career Change</h3>
                    <p>To many of our clients, one of the most stressful parts of a career change is when they are trying to decide on whether or not to actually make the...</p>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
    
@if (Auth::guest())
    <div class="container">

        <div class="row well">
            <div class="col-lg-8 col-md-8">
                <h4>BrilliantU</h4>
                <p>Register Now!</p>
            </div>
            <div class="col-lg-4 col-md-4">
                <a class="btn btn-lg btn-primary pull-right" href="{{url('register')}}">Register!</a>
            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
@endif
    <div class="container">

        <hr>

        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; BrilliantU {{{date('Y')}}}</p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->
@stop