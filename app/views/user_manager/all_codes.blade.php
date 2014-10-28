@extends($layout)

@section('content')
<div id='ajax-content'>
    {{View::make('user_manager.all_codes_partial')->withCodes($codes)}}
</div>
@stop