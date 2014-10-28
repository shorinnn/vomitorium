@extends($layout)

@section('content')
   
<div class="section">

        <div class="container">
            @if(isset($meta['hash']) && $meta['hash']=='')
                <h1>Oops, not cool</h1><br />
                <h2>This link is not valid.</h2>
            @else
            <h1>{{ $title or '' }}</h1>
            {{ $form }}
            @endif
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop