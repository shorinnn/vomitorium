<?php
$programs = get_programs();
?>
@if($programs!=null && $programs->count() > 0)
<li class='program_chooser'>
    Current Program:<br />
    <select class="form-control" id='program_chooser' onchange='choose_program()'>
        <option value='0'>Programs Picker</option>
        @foreach($programs as $p)
        <?php
        $selected = '';
        if (Session::has('program_id') && Session::get('program_id') == $p->id)
            $selected = 'selected="selected"';
        ?>
        <option title="{{$p->name}}" value='{{$p->id}}' {{$selected}}>{{Str::limit($p->name, 20)}}</option>
        @endforeach
    </select>
</li>
@endif


    @if(Request::url() == url(""))
<li class="active"><a href="{{url('/')}}">Dashboard</a></li>
@else
<li><a href="{{url('/')}}">Dashboard</a></li>
@endif

@if(current_controller()=='Skills' || current_controller()=='Programs' ||
current_controller()=='Lessons' || current_controller()=='Modules'
|| current_controller()=='Chapters'|| current_controller()=='Categories')
<li class="dropdown active">
    @else
<li class="dropdown">
    @endif
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Program Tools <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li>{{link_to('programs','Programs')}}</li>
        @if(Session::has('program_id'))
            <li>{{link_to('payment_plans','Payment Plans')}}</li>
        @endif
        <li>{{link_to('modules','Modules')}}</li>                                   
        <li>{{link_to('lessons','Standalone Lessons')}}</li>                                   
<!--        <li>{{link_to('lessons','Course Editor')}}</li>                                   
        <li>{{link_to('chapters','Chapter Editor')}}</li>-->
        <li>{{link_to('categories','Block Category Manager')}}</li>                                    
        <li>{{link_to('skills','Skill-block Options')}}</li>
        <li>{{link_to('reports','Reports')}}</li>
    </ul>
</li>
    @if(current_controller()=='UserManager')
<li class=" active">
    @else
<li>
    @endif
    {{link_to('users','Clients')}}
</li>

@if(Request::url() == url("contact-us"))
<li class="active"><a href="{{url('contact-us')}}">Support</a></li>
@else
<li><a href="{{url('contact-us')}}">Support</a></li>
@endif

