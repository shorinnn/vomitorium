@if(sys_settings('send_code_email')=='')
Hey {{$first_name.' '.$last_name}}
<br />
Please use the following link <a href='{{$link}}'>{{$link}}</a> to register for {{$program_name}}
@else
{{$content}}
@endif