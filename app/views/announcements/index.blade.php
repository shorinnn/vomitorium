@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            <button type="button" class="btn btn-primary create-form-btn btn-default" onclick="create_form()">Add New</button>
            <div class="add_form">
            <form method="POST" action="{{action("AnnouncementsController@store")}}" accept-charset="UTF-8" id="create_form">
                <fieldset>
                    <div class="form-group">
                        <label for="title">Announcement</label>
                        <input autofocus="autofocus" class="form-control" tabindex="1" 
                               placeholder="e.g. Hear yee, hear yee..." type="text" name="content" id="content" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Start Date</label>
                        <input autofocus="autofocus" class="form-control datepikr" tabindex="1" value="{{date('m/d/Y')}}"
                              type="text" name="start_date" id="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="title">End Date</label>
                        <input autofocus="autofocus" class="form-control datepikr" tabindex="1" value="{{date('m/d/Y', time() + 24*60*60)}}"
                              type="text" name="end_date" id="end_date" required>
                    </div>
                    <div class="form-group">
                        <button tabindex="3" type="submit" class="btn btn-default">Submit</button>
                    </div>
                </fieldset>
            </form>
            </div>
            <br />
            <br />
            <table class='table table-striped table-bordered'>
                <thead>
                    <tr class="list-row"><th>Announcement</th><th>Start Date</th><th>End Date</th><th>Active</th><th>Delete</th></tr>
                </thead>
                <tbody>
                    @foreach($announcements as $a)
                    {{View::make('announcements.announcement')->withAnnouncement($a)}}
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    <script>
        is_lesson_editor = true;
    </script>
@stop