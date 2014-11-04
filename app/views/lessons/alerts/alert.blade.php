<tr class='row-{{$alert->id}}'>
    <td style='padding-top:15px!important; padding-left:15px!important; width:10px;'>
        @if($alert->delivery_type=='email')
        <i class='glyphicon glyphicon-envelope do-tooltip' data-url="{{action('LessonAlertsController@update', $alert->id)}}" title='Email Alert. Click to change alert type' data-id='{{$alert->id}}' onclick="change_alert_type(this)"></i>
        @else
        <i class='glyphicon glyphicon-phone do-tooltip' data-url="{{action('LessonAlertsController@update', $alert->id)}}" title='Text Alert. Click to change alert type'  data-id='{{$alert->id}}' onclick="change_alert_type(this)"></i>
        @endif
    </td>
    <td style='width:10px'>
        <input type='text' class='form-control width-50' value='{{$alert->time_value}}'
               data-ui-field="this" data-url="{{action('LessonAlertsController@update', $alert->id)}}"
               data-method="PUT" data-field="time_value" data-pk="{{$alert->id}}"
               onkeyup="delay(ajax_btn_update,200, this)" /></td>
    <td  style='width:110px'>
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
    <td style='width:170px'><input type='text' class='form-control' value='{{$alert->subject}}'
               data-ui-field="this" data-url="{{action('LessonAlertsController@update', $alert->id)}}"
               data-method="PUT" data-field="subject" data-pk="{{$alert->id}}"
               onkeyup="delay(ajax_btn_update, 300, this)" /> 
    </td>
    <td>
        <button class="btn btn-primary" onclick='edit_alert_content({{$alert->id}})'>Edit</button>
        <button data-toggle="tooltip" title="" class="do-tooltip btn btn-danger btn-warning delete-btn" 
                data-id='{{$alert->id}}' data-target='row' data-width='90px' data-confirm-text='Delete?'
                data-url='{{action('LessonAlertsController@destroy', $alert->id)}}'
                data-original-title="Delete notification">
                <i class="glyphicon glyphicon-trash"></i>
      </button>
    </td>
</tr>