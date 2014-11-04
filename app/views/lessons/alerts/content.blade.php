<textarea class='form-control' rows="8"
          value='{{$alert->content}}'
               data-ui-field="this" data-url="{{action('LessonAlertsController@update', $alert->id)}}"
               data-method="PUT" data-field="content" data-pk="{{$alert->id}}"
               onkeyup="delay(ajax_btn_update,400, this)" >{{$alert->content}}</textarea>