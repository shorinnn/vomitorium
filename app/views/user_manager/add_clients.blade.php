<div class='add'>
                    <input type="radio" name="type" value="link" id='register_link' onclick='show_div(this)' /><label for='register_link'>Send Registration Link (Email)</label><br />
                    <input type="radio" name="type" value="link" id='register_manual' onclick='show_div(this)' /> <label for='register_manual'>Manual Add</label><br />
                    <input type="radio" name="type" value="link" id='register_codes' onclick='show_div(this)' /> <label for='register_codes'>Generate Access Passes</label><br />
                    <input type="radio" name="type" value="link" id='send_codes' onclick='show_div(this)' /> <label for='send_codes'>Send Access Pass (Email)</label><br />
                    <form id='register_link_div' 
                          data-bv-message =""
                          data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                          data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                          data-bv-feedbackicons-validating="glyphicon glyphicon-refresh"
                          class="nodisplay ajax-form" method="post" action='{{url('users/register')}}'>
<br />                        
<div class='row'>
                            <div class="col-lg-12 form-group">
                                <label>Program - Payment Plan</label>
                                @if($plans->count()==0)
                                    You have no payment plans created. <a href="{{url('payment_plans')}}">Create one</a>
                                @else
                                    <select name='payment_plan' class="form-control">
                                         @foreach($plans as $p)
                                             <option title="{{$p->name}}" value='{{$p->id}}'>{{$p->program->name}} - {{$p->name}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 form-group">
                                <label>Email</label>
                            <input type='text' name='email'
                                   data-bv-notempty 
                                   data-bv-emailaddress="true"
                                   class='form-control' />
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-lg-6 form-group'>
                                <label>First Name</label>
                                <input type='text' name='first_name' data-bv-notempty class="form-control" />
                            </div>
                            <div class='col-lg-6 form-group'>
                                <label>Last Name (Optional)</label>
                                <input type='text' name='last_name' 
                                       data-bv-callback="true"
                                       data-bv-callback-callback="resettable" class="form-control" />
                            </div>
                        </div>
                        <br />
                        <button type='button' class='btn btn-sm-default btn-default' onclick='slideToggle(".link-email")'>Edit Email Content</button><br />
                        <div class='nodisplay link-email'>
<textarea id='registration_text' class='summernote_editor'>@if(sys_settings('send_registration_email')=='')
Hey [FIRST_NAME] [LAST_NAME]
                            <br />
                            Please use the following link <a href='[LINK]'>[LINK]</a> to register for [PROGRAM_NAME]
                            @else
{{sys_settings('send_registration_email')}}
                            @endif</textarea><br />
                            <center><button type='button' class='btn btn-sm btn-primary' data-ui-field='#registration_text' 
                            data-field='send_registration_email' data-pk='1' 
                            data-url='{{url('system_settings')}}' onclick='ajax_btn_update(this)'>Save Template</button></center>
                            <br />
                        </div>
                        <button class='btn btn-default' data-submit='1'>Send</button>
                        <input type='hidden' name='type' value='link' />
                    </form>
                    <form id='register_manual_div' 
                          data-bv-message =""
                          data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                          data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                          data-bv-feedbackicons-validating="glyphicon glyphicon-refresh"
                          class="nodisplay ajax-form" method="post"  action='{{url('users/register')}}'>
                        <br />
                        <div class='row'>
                            <div class="col-lg-12 form-group">
                            <label>Program</label>
                            <select name='program' class="form-control">
                                     @foreach($programs as $p)
                                        <option title="{{$p->name}}" value='{{$p->id}}'
                                        @if(Session::has('program_id') && Session::get('program_id')==$p->id)
                                        selected="selected"
                                        @endif
                                        >{{$p->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 form-group">
                                <label>Email</label>
                            <input type='text' name='email' class='form-control' 
                                   data-bv-emailaddress="true"
                                   data-bv-notempty
                                   />
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 form-group">
                            <label>Username</label>
                            <input type='text' name='username' class='form-control' 
                                   data-bv-notempty  />
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 form-group">
                            <label>Password</label>
                            <input type='text' name='password' data-bv-notempty  class='form-control' value='{{Str::random()}}' />
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-lg-6 form-group'>
                                <label>First Name</label>
                                <input type='text' name='first_name' data-bv-notempty  class="form-control" />
                            </div>
                            <div class='col-lg-6 form-group'>
                                <label>Last Name (Optional)</label>
                                <input type='text' name='last_name' 
                                       data-bv-callback="true"
                                       data-bv-callback-callback="resettable" class="form-control" />
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12">
                                <label>User Level</label>
                                <select class='form-control' name='level'>
                                    <option value='2'>Member</option>
                                    <option value='1'>Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 text-center">
                                <input type='checkbox' name='send_email' value = '1' id='send_email' /> <label for='send_email'>Send Email</label>
                            </div>
                        </div>
                        <br />
                        <button type='button' class='btn btn-sm-default btn-default' onclick='slideToggle(".manual-add")'>Edit Email Content</button><br />
                        <div class='nodisplay manual-add'>
<textarea id='manual_registration_email' class='summernote_editor'>@if(sys_settings('manual_registration_email')=='')
Hey [FIRST_NAME] [LAST_NAME]
<br />
Just wanted to let you know we've registered you for [PROGRAM_NAME].<br />
Feel free to log in here: <a href='[LINK]'>[LINK]</a> <br />
Your password is [PASSWORD]
                            @else
{{sys_settings('manual_registration_email')}}
                            @endif</textarea><br />
                            <center><button type='button' class='btn btn-sm btn-primary' data-ui-field='#manual_registration_email' 
                            data-field='manual_registration_email' data-pk='1' 
                            data-url='{{url('system_settings')}}' onclick='ajax_btn_update(this)'>Save Template</button></center>
                        </div>
                        <br />
                        
                        <button class='btn btn-default' data-submit='1'>Add</button>
                        <input type='hidden' name='type' value='manual' />
                    </form>
                    <form id='register_codes_div' 
                          data-bv-message =""
                          data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                          data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                          data-bv-feedbackicons-validating="glyphicon glyphicon-refresh"
                          class="nodisplay ajax-form code-form" method="post"  action='{{url('users/register')}}'>
                        <br />
                         <div class='row'>
                            <div class="col-lg-12">
                                <label>Program</label>
                            <select name='program' class="form-control">
                                     @foreach($programs as $p)
                                        <option title="{{$p->name}}" value='{{$p->id}}'
                                        @if(Session::has('program_id') && Session::get('program_id')==$p->id)
                                        selected="selected"
                                        @endif
                                        >{{$p->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class='row'>
                            <div class='col-lg-12 text-center form-group'>
                                <label>How many codes do you want to generate?</label>
                                <div class='row'>
                                    <div class='col-lg-4'></div>
                                    <div class='col-lg-4'>
                                        <input type='text' class='form-control form-inline' name='code_count'
                                               data-bv-notempty 
                                               data-bv-integer
                                               style='width:80%; margin-left: auto; margin-right: auto; text-align: center' />
                                    </div>
                                    <div class='col-lg-4'></div>
                                </div>
                            </div>
                        </div>
                        
                    <div class='col-lg-12 form-group'>
                            <button class="btn btn-default"  data-submit='1'>Generate</button>
                        </div>
                        <input type='hidden' name='type' value='code' />
                    </form>
                    <br class="clear_fix" />
            </div>
            <form id='send_codes_div' 
                          data-bv-message =""
                          data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
                          data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                          data-bv-feedbackicons-validating="glyphicon glyphicon-refresh"
                          class="nodisplay ajax-form code-form" method="post"  action='{{url('users/register')}}'>
                        <br />                        
<div class='row'>
                            <div class="col-lg-12 form-group">
                                <label>Program</label>
                                <select name='program' class="form-control">
                                     @foreach($programs as $p)
                                        <option title="{{$p->name}}" value='{{$p->id}}'
                                        @if(Session::has('program_id') && Session::get('program_id')==$p->id)
                                        selected="selected"
                                        @endif
                                        >{{$p->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 form-group">
                                <label>Email</label>
                            <input type='text' name='email'
                                   data-bv-notempty 
                                   data-bv-emailaddress="true"
                                   class='form-control' />
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-lg-6 form-group'>
                                <label>First Name</label>
                                <input type='text' name='first_name' data-bv-notempty class="form-control" />
                            </div>
                            <div class='col-lg-6 form-group'>
                                <label>Last Name (Optional)</label>
                                <input type='text' name='last_name' 
                                       data-bv-callback="true"
                                       data-bv-callback-callback="resettable" class="form-control" />
                            </div>
                        </div>
                        <br />
                        <button type='button' class='btn btn-sm-default btn-default' onclick='slideToggle(".link-email")'>Edit Email Content</button><br />
                        <div class='nodisplay link-email'>
<textarea id='code_text' class='summernote_editor'>@if(sys_settings('send_code_email')=='')
Hey [FIRST_NAME] [LAST_NAME]
                            <br />
                            Please use the following link <a href='[LINK]'>[LINK]</a> to access [PROGRAM_NAME]
                            @else
{{sys_settings('send_code_email')}}
                            @endif</textarea><br />
                            <center><button type='button' class='btn btn-sm btn-primary' data-ui-field='#code_text' 
                            data-field='send_code_email' data-pk='1'  data-method="POST"
                            data-url='{{url('system_settings')}}' onclick='ajax_btn_update(this)'>Save Template</button></center>
                            <br />
                        </div>
                        <button class='btn btn-default' data-submit='1'>Send</button>
                        <input type='hidden' name='type' value='send_code' />
                    </form>
                    <br class="clear_fix" />
            </div>
            
