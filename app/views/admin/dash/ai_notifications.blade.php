@if($current_program->launched==0)
<div id='launch_options' class='alert  text-center'>
    It looks like your program is still in the works but we might be wrong!<br /><br />
    <button class="btn btn-default inline" onclick='launch_program("{{url('programs/update')}}",{{$current_program->id}})'>Launch It!</button> OR
    <a href='{{url('modules')}}' class="btn btn-default inline">Continue Editing Course</a>
</div>
@endif