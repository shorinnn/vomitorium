@extends($layout)

@section('content')
   
<div class="section">
        <div class="container">
            <!--
            <button type="button" class="btn btn-primary create-form-btn" onclick="create_form()">Add New</button>
            <div class="add_form">
            <form method="POST" action="{{action("ChaptersController@store")}}" accept-charset="UTF-8" id="add_form">
                <fieldset>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="Title" type="text" name="title" id="title" required>
                    </div>
                    <div class="form-group">
                        <button tabindex="3" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </fieldset>
            </form>
            </div>-->
            {{View::make('chapters.chapters')->withData($data)}}
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop