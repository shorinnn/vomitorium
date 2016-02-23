@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            <table class='table table-bordered table-striped'>
                <tr><td>Installation</td><td>{{sys_settings('installation')}}</td></tr>
                <tr><td>Title</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='title'>{{sys_settings('title')}}</a></td></tr>
                <tr><td>Dash Layout</td>
                    <td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' 
                           data-type='select' data-source='[{"template_1":"Full"},{"template_2":"Not Yet Reviewed & Messages"},{"template_3":"Except Messages"}]' 
                           data-value="{{sys_settings('dash_layout')}}"
                           id='dash_layout'>{{human_dash()}}</a></td></tr>
                <tr><td>Domain Name</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='domain'>{{sys_settings('domain')}}</a></td></tr>
                <tr><td>Contact Us Destination Email</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='contact_email'>{{sys_settings('contact_email')}}</a></td></tr>
                <tr><td>Contact Us Destination Name</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='contact_name'>{{sys_settings('contact_name')}}</a></td></tr>
                <tr><td>Outgoing Email Address</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='email_from'>{{sys_settings('email_from')}}</a></td></tr>
                <tr><td>Outgoing Email Name</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='email_name'>{{sys_settings('email_name')}}</a></td></tr>
                <tr><td>Client Term</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='client_term'>{{sys_settings('client_term')}}</a></td></tr>
                <tr><td>Autosave Question</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='autosave_question'>{{sys_settings('autosave_question')}}</a></td></tr>
                <tr><td>Autosave Yes button label</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='autosave_yes_button_label'>{{sys_settings('autosave_yes_button_label')}}</a></td></tr>
                <tr><td>Autosave No button label</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='autosave_no_button'>{{sys_settings('autosave_no_button')}}</a></td></tr>
                <tr><td>Above Lesson Progressbar Content</td>
                    <td>
                        Current: 
                        <blockquote>
                            {{{ sys_settings('above_progressbar_content') }}}
                        </blockquote>
                        <input type="text" class="form-control inline" value='' onchange="ajax_update(this)" data-field="above_progressbar_content" id="above_progressbar_content" data-pk="1" 
                               data-url="{{ url('system_settings') }}" placeholder="Enter New Value" />
                    </td></tr>
                <tr><td>User Dashboard HTML</td>
                    <td>
                        Current: 
                        <blockquote>
                            {{{ sys_settings('user_dash_html') }}}
                        </blockquote>
                        <input type="text" class="form-control inline" value='' onchange="ajax_update(this)" data-field="user_dash_html" id="user_dash_html" data-pk="1" 
                               data-url="{{ url('system_settings') }}" placeholder="Enter New Value" />
                    </td></tr>
                <tr><td>New Comment Email<br />
                        <p class='alert alert-info'>
                            Available tags: [FirstName], [LastName], [CoachFirstName], [CoachLastName], [Link]
                        </p>
                    </td><td><a href='#' data-url='{{url('system_settings')}}' data-type='textarea' data-mode='inline' class='editable' data-pk='1' id='new_comment_email'>{{sys_settings('new_comment_email')}}</a></td></tr>
                <tr><td>Purchased Program Email<br />
                        <p class='alert alert-info'>
                            Available tags: [CustomerFirstName], [CustomerLastName], [ProgramName], [LoginLink]
                        </p>
                    </td><td><a href='#' data-url='{{url('system_settings')}}' data-type='textarea' data-mode='inline' 
                                class='editable' data-pk='1' id='purchase_email_content'>{{sys_settings('purchase_email_content')}}</a></td></tr>
                <tr><td>Purchase Program Email Subject Line</td><td><a href='#' data-url='{{url('program_purchase_email_subject')}}' data-mode='inline' class='editable' data-pk='1' id='program_purchase_email_subject'>{{sys_settings('program_purchase_email_subject')}}</a></td></tr>
                
                <tr><td>Free Program Register Email<br />
                        <p class='alert alert-info'>
                            Available tags: [CustomerFirstName], [CustomerLastName], [ProgramName], [LoginLink]
                        </p>
                    </td><td><a href='#' data-url='{{url('system_settings')}}' data-type='textarea' data-mode='inline' 
                                class='editable' data-pk='1' id='free_register_email_content'>{{sys_settings('free_register_email_content')}}</a></td></tr>
                <tr><td>Free Program Register Email Subject Line</td><td><a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='free_register_email_subject'>{{sys_settings('free_register_email_subject')}}</a></td></tr>
                <tr><td>Custom Register Page Header Link
                    <br />eg: {{url('register/ABC123')}}
                    </td><td>
                        {{ url('register/') }}/<a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='custom_register_page_link'>{{sys_settings('custom_register_page_link')}}</a></td></tr>
            </table>
          
       </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop