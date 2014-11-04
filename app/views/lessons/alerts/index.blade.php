<button class='btn btn-xs btn-primary' onclick='new_item(this)' data-url='{{action('LessonAlertsController@store')}}'
        data-data='{{json_encode(array('lesson_id'=>$lesson->id))}}' data-appendto=".deadline-table">Add New</button><br /><br />
<table class='table deadline-table table-striped'>
    @foreach($lesson->alerts as $a)
        {{View::make('lessons.alerts.alert')->withAlert($a)}}
    @endforeach
</table>