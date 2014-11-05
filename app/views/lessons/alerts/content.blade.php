<div class='alert-div alert-content-{{$alert->id}}' data-ui-field=".alert-content-{{$alert->id}} .summernote_editor"
data-url="{{action('LessonAlertsController@update', $alert->id)}}"
data-method="PUT" data-field="content" data-pk="{{$alert->id}}"  onkeyup="delay(ajax_btn_update,500, this)">
<textarea class='summernote_editor' rows="8" value='{{$alert->content}}'>{{$alert->content}}</textarea>
</div>