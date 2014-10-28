@extends($layout)

@section('content')

        <div class="container" id="ajax-content">
            {{View::make('categories.categories')->withCats($cats)}}
        </div>
        <!-- /.container -->
    </div>
    <!-- /.section -->
@stop