@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            <table class='table table-bordered table-striped'>
                <tr><td>Logo</td>
                    <td>
                         @if(sys_settings('logo')!='')
                        <a href="{{url('assets/img/logos/'.sys_settings('logo'))}}" target="_blank">
                            <img id='logo-preview' src="{{url('assets/img/logos/'.sys_settings('logo'))}}" height="100" /><br /><br />
                        </a>
                        @else
                        <a href="{{url('assets/img/logos/'.sys_settings('logo'))}}" target="_blank">
                            <img id='logo-preview' height="100" /><br /><br />
                        </a>
                        @endif
                            <input type="file" name="file" id='logo' /> 
                            <input type="button" class="btn btn-default" value="Upload Logo" onclick='upload_logo()' />
                    </td></tr>
                <tr><td>Background Image</td>
                    <td>
                        @if(sys_settings('bgimage')!='')
                        <a href="{{url('assets/img/backgrounds/'.sys_settings('bgimage'))}}" target="_blank">
                            <img id='bg-preview' src="{{url('assets/img/backgrounds/'.sys_settings('bgimage'))}}" height="100" /><br /><br />
                        </a>
                        @else
                        <a href="{{url('assets/img/backgrounds/'.sys_settings('bgimage'))}}" target="_blank">
                            <img id='bg-preview' height="100" /><br /><br />
                        </a>
                        @endif
                        <input type="file" name="file" id='file' /> <input type="button" class="btn btn-default" value="Upload Background" onclick='upload_background()' />
                    </td></tr>
                <!--<tr><td>Banner Text Foreground Color</td><td>#<a href='#' data-url='{{url('system_settings')}}' data-mode='inline' class='editable' data-pk='1' id='tagline_foreground_color'>{{sys_settings('tagline_foreground_color')}}</a></td></tr>-->
                <tr><td>Background Color</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('tagline_background_color')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('tagline_background_color')}}" 
                                 onchange='ajax_update(this)' data-field = 'tagline_background_color' data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('tagline_background_color')}}"></i></span>
			</div>
                </tr>
                <tr><td>Color #1 (Default <div class="sample sample-1" style='background-color:#1e6e37' 
                                               onclick="update_color_field('#custom_color_1','#1e6e37')"></div>)</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('custom_color_1')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('custom_color_1')}}" 
                                 onchange='ajax_update(this)' data-field = 'custom_color_1' id = 'custom_color_1' data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('custom_color_1')}}"></i></span>
			</div>
                </tr>
                <tr><td>Color #2 (Default <div class="sample sample-2"  style='background-color:#33443e'
                                               onclick="update_color_field('#custom_color_2','#33443e')"></div>)</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('custom_color_2')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('custom_color_2')}}" 
                                 onchange='ajax_update(this)' data-field = 'custom_color_2' id = 'custom_color_2' data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('custom_color_2')}}"></i></span>
			</div>
                </tr>
                <tr><td>Color #3 (Default <div class="sample sample-3"  style='background-color:#52b963'
                                               onclick="update_color_field('#custom_color_3','#52b963')"></div>)</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('custom_color_3')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('custom_color_3')}}" 
                                 onchange='ajax_update(this)' data-field = 'custom_color_3'  id = 'custom_color_3'  data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('custom_color_3')}}"></i></span>
			</div>
                </tr>
                <tr><td>Color #4 (Default <div class="sample sample-4"  style='background-color:#489C56'
                                               onclick="update_color_field('#custom_color_4','#489C56')"></div>)</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('custom_color_4')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('custom_color_4')}}" 
                                 onchange='ajax_update(this)' data-field = 'custom_color_4'  id = 'custom_color_4'  data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('custom_color_4')}}"></i></span>
			</div>
                </tr>
                <tr><td>Color #5 (Default <div class="sample sample-5"  style='background-color:#78e9f1'
                                               onclick="update_color_field('#custom_color_5','#78e9f1')"></div>)</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('custom_color_5')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('custom_color_5')}}" 
                                 onchange='ajax_update(this)' data-field = 'custom_color_5'  id = 'custom_color_5'  data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('custom_color_5')}}"></i></span>
			</div>
                </tr>
                <tr><td>Color #6 (Default <div class="sample sample-6"  style='background-color:#f8f8f8'
                                               onclick="update_color_field('#custom_color_6','#f8f8f8')"></div>)</td>
                    <td>
                       <div class="input-append color colorpicker" data-color="{{sys_settings('custom_color_6')}}">
				<input type="text" class="span2 input-xs form-control inline" value="{{sys_settings('custom_color_6')}}" 
                                 onchange='ajax_update(this)' data-field = 'custom_color_6'  id = 'custom_color_6'  data-pk = '1'
                                   data-url='{{url('system_settings')}}' >
				<span class="add-on"><i style="background-color: {{sys_settings('custom_color_6')}}"></i></span>
			</div>
                </tr>
            </table>
          
       </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop