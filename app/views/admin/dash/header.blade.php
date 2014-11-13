<div class='quick_stats row text-center'>
    
    <h3>{{$current_program->name}}</h3>
    
    <div class='col-lg-4'><h2>{{$current_program->today_users()}}</h2> 
        
        New 
        {{singplural($current_program->today_users(), client_term() ) }}
        Today</div>

    <div class='col-lg-4'>
        <h2>{{$current_program->users()->count()}}</h2>
        {{singplural($current_program->users()->count(), client_term()) }}
    </div>
    <div class='col-lg-4'>
        <a href='{{url('modules')}}'>
            <i class='glyphicon glyphicon-edit'></i>
            Editor</a>
        <a href='{{url('inbox')}}'>
            <i class='glyphicon glyphicon-envelope'></i>
            Inbox <span>({{PMController::inbox_count()}})</span></a>
    </div>
</div>
<div class='clearfix'></div>