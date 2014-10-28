@extends($layout)
@section('content')
 <!-- Page Content -->

    <div class="section">
        <div class="container">
            We know you're excited but unfortunately this lesson is not available 
            @if($days>1)
                for another {{$days}} days.
            @else
                until tomorrow.
            @endif
            <br />See you then.
        </div>
    </div>
@stop