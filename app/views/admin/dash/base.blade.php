@extends($layout)

@section('content')
<div class="section">


        <div class="container">
            
            @if(Lesson::count()==0 && !Session::has('program_id'))
            <h2 class="text-center" style="margin-bottom: 15px;">Welcome {{Auth::user()->first_name}} to 
                <img style="height:28px" src="{{url('assets/img/TrainingAutomate_logo.png')}}" /></h2> 
                 <div class='text-center'>
                     <p class='tip bold'>Tip: A program can be a one-time delivered product, a 4-week course, or anything you can 
                         imagine and do with this really.
                     </p>
                    
                     <div id="create_program_div">
                        <button class="btn btn-lg btn-default create-form-btn do-tooltip" title="Do it {{Auth::user()->first_name}}!"  data-placement="right" onclick="create_program(1)" >Create Program</button><br />
                        <p style="font-size:50px; margin:10px 0 0 0 !important">
                            <i style="font-size:50px; color: #00CC00" class="glyphicon glyphicon-arrow-up"></i>
                        </p>
                        <b>Click here to begin!</b>
                     </div>
                </div>
                <div style="display:none">
                        
                    <div id="program">
                        <p class="step-status">Step 1</p>
                        Name Your First Program:<br /><br />
                        <input type="text" class="form-control" id="program_name" placeholder="e.g. {{Auth::user()->first_name}}'s Kickass Course" /><br />
                        <button class="btn btn-danger do-tooltip" onclick="create_program(3)" title="Almost there..."> 
                            Next <i class='glyphicon glyphicon-forward'></i></button>
                    </div>
                    <div id="lesson">
                        <p class="step-status">Fill this in, then it's off to the lesson editor!</p>
                        Name The First Lesson:<br /><br />
                        <input type="text" class="form-control" id="lesson_name" placeholder="e.g. Week 1 - Introduction" /><br />
                        <button class="btn btn-danger" onclick="create_program(3)"> Next <i class='glyphicon glyphicon-forward'></i></button>
                    </div>
                </div>
            @else
            
            <center><img style="height:28px" src="{{url('assets/img/TrainingAutomate_logo.png')}}" /></center>
            
                @if(Session::has('program_id'))
                    {{View::make('admin.dash.ai_notifications')->withCurrent_program($current_program)}}
                    <div class='dash-partial'>
                        
                        {{View::make('admin.dash.'.sys_settings('dash_layout','template_1'))->withUnattended($unattended)
                              ->withCurrent_program($current_program)
                              ->withNewest($newest)->withNew_submissions($new_submissions)->withPm($pm) }}
                        <br class='clearfix clear_fix' /></div>
                @else
                <div class='text-center'>
                    <h2>Your Programs</h2>
                    <h4 class='text-center'>Select one</h4>
                    @foreach(get_programs() as $p)
                    
                        <div class='program_slot' onclick="choose_program_id({{$p->id}})">
                            <div class='program_title'>
                            {{$p->name}}
                            </div>
                            <p class='program_users'> 
                                {{$p->users()->count()}} {{singplural($p->users()->count(), 'Clients')}}
                            </p>
                        </div>
                    @endforeach
                   
                    <br class="clear_fix" />
                    
                    <div class="add_form">
                    <form method="POST" action="{{action("ProgramsController@store")}}" accept-charset="UTF-8" id="create_form">
                        <input type="hidden" name="just_program" value="1" />
                        <input type="hidden" name="from_dash" id="from_dash" value="1" />
                        <fieldset>
                            <div class="form-group">
                                <label for="title">What is the Program Name?</label>
                                <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="e.g. Another Awesome Program" type="text" name="program" id="program" required>
                            </div>
                            <div class="form-group">
                                <button tabindex="3" type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </fieldset>
                    </form>
                    </div>
                    <button class='btn btn-lg btn-default create-form-btn' onclick="create_form()">Create New Program</button>
                </div>
                @endif
            @endif
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop