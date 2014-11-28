@extends($layout)

@section('content')
<a href="{{ url('users/comma_separated_codes') }}" target="_blank">View comma-separated available codes list</a>
<div id='ajax-content'>
    {{View::make('user_manager.all_codes_partial')->withCodes($codes)}}
</div>
@stop