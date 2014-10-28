@if(sys_settings('manual_registration_email')=='')
Hey {{$first_name.' '.$last_name}}
<br />
Just wanted to let you know we've registered you for {{$program_name}}.<br />
Feel free to log in here: <a href='{{$link}}'>{{$link}}</a> 
@else
{{$content}}
@endif