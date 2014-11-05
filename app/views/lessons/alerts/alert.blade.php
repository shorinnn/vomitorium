<tr class='row-{{$alert->id}}'>
    <td style='padding-top:15px!important; padding-left:15px!important; width:10px;'>
        <!--
        -->
        <i class='glyphicon
        @if($alert->delivery_type=='email')
            glyphicon-envelope
        @else 
            glyphicon-phone
        @endif
         do-popover do-tooltip alert-type-{{$alert->id}}' 
           data-title-noconflict="Text Alert. Click to change alert type" data-html="true"
           data-content='
           <p style="text-align:center; font-size:20px;">
           <i class="glyphicon glyphicon-envelope do-tooltip" title="Email notification" onclick="change_alert_type(this)"
           data-id="{{$alert->id}}" data-url="{{action('LessonAlertsController@update', $alert->id)}}" ></i> 
           <i class="glyphicon glyphicon-phone do-tooltip" title="Text notification" onclick="change_alert_type(this)"
           data-id="{{$alert->id}}" data-url="{{action('LessonAlertsController@update', $alert->id)}}" ></i></p>'  
           ></i>
       
    </td>
    <td style='width:10px'>
        <input type='text' class='form-control width-50' value='{{$alert->time_value}}'
               data-ui-field="this" data-url="{{action('LessonAlertsController@update', $alert->id)}}"
               data-method="PUT" data-field="time_value" data-pk="{{$alert->id}}"
               onkeyup="delay(ajax_btn_update,200, this)" /></td>
    <td>
        {{Form::select('time_unit', 
                  array('day' => 'day(s)', 'hour' => 'hour(s)'), 
                  $alert->time_unit,
                  array(
                    'class' => "form-control",
                    'data-ui-field' => "this",
                    'data-url' => action('LessonAlertsController@update', $alert->id),
                    'data-method' => "PUT",
                    'data-field' => "time_unit",
                    'data-pk' => $alert->id,
                    'onchange' => "delay(ajax_btn_update,100, this)"
                    )
                ) }}
    </td>
    <td><input type='text' class='form-control' value='{{$alert->subject}}'
               data-ui-field="this" data-url="{{action('LessonAlertsController@update', $alert->id)}}"
               data-method="PUT" data-field="subject" data-pk="{{$alert->id}}"
               onkeyup="delay(ajax_btn_update, 300, this)" /> 
    </td>
    <td style="width:170px">
        <button class="btn btn-primary" onclick='edit_alert_content({{$alert->id}})'>Edit</button>
        <button data-toggle="tooltip" title="" class="do-tooltip btn btn-danger btn-warning delete-btn" 
                data-id='{{$alert->id}}' data-target='row' data-width='90px' data-confirm-text='Delete?'
                data-url='{{action('LessonAlertsController@destroy', $alert->id)}}'
                data-original-title="Delete notification">
                <i class="glyphicon glyphicon-trash"></i>
      </button>
    </td>
</tr>