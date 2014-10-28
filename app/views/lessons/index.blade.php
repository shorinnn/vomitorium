@extends($layout)

@section('content')
<div class="section">
    <form method="POST" action="{{action("ChaptersController@store")}}" accept-charset="UTF-8" class="form" style="display:none">
    <fieldset>
        <div class="form-group">
            <label for="title">Title</label>
            <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="Title" type="text" name="title" id="title" required>
        </div>
        <div class="form-group">
            <button tabindex="3" type="submit" class="btn btn-primary">Add Chapter</button>
        </div>
    </fieldset>
</form>

        <div class="container">
            <div class="add_form">
            <form method="POST" action="{{action("LessonsController@store")}}" accept-charset="UTF-8" id="add_form">
                <fieldset>
                    <div class="form-group">
                        <label for="title">What is the Lesson Title?</label>
                        <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="e.g. Introduction to Herb Illuminators" type="text" name="title" id="title" required>
                    </div>
                    <!--<div class="form-group">
                        <label for="meta_keywords">Meta Keywords</label>
                        <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="Keywords" type="text" name="meta_keywords" id="meta_keywords">
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="Description" type="text" name="meta_description" id="meta_description">
                    </div>-->
                    <div class="form-group">
                        <button tabindex="3" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </fieldset>
            </form>
            </div>
            {{View::make('lessons.lessons')->withData($data)}}
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop