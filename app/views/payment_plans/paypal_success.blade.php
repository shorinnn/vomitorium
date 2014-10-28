@extends($layout)

@section('content')
<div class="section">
        <div class="container">
            <p class='alert alert-success'>
                Thank you. Please wait while Paypal confirms your payment (this page will auto-refresh).<br />
                <center><img src='{{url('assets/img/ajax-loader_1.gif')}}' /></center>
            </p>
           
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    <script>
        onload_functions = ['paypal_check'];
    </script>
@stop