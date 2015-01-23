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
                <tr><td>New Comment Email<br />
                        <p class='alert alert-info'>
                            Available tags: [FirstName], [LastName], [CoachFirstName], [CoachLastName], [Link]
                        </p>
                    </td><td><a href='#' data-url='{{url('system_settings')}}' data-type='textarea' data-mode='inline' class='editable' data-pk='1' id='new_comment_email'>{{sys_settings('new_comment_email')}}</a></td></tr>
            </table>
          
       </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop