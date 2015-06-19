@extends($layout)

@section('content')

<div class="section">
        <div class="container">
            <p class='alert alert-error alert-danger'>
                Your card was declined. <a href='{{url('/')}}'>Go Back</a>
                {{$exception->getMessage()}}
            </p>
           <!--
           -->
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
@stop