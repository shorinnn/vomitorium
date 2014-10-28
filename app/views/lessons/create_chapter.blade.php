<form method="POST" action="{{action("LessonsController@store_chapter")}}" accept-charset="UTF-8" id="add_form">
    <input type='hidden' name='lesson' value='{{$lesson}}' />
    <fieldset>
        <div class="form-group">
            <label for="title">Chapter Title</label>
            <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="Title" type="text" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="order">Place Chapter</label><br />
            <select id='chapter_order' name='order' class='form-control pull-left' style='width:30%' onchange="toggle_chapter_ref()">
                <option value='at_the_end'>At The End</option>
                <option value='before'>Before</option>
                <option value='after'>After</option>
            </select>
            <select id='chapter_ref' name='chapter' class='form-control pull-right' style='width:69%'>
                <option value='0'></option>
                @foreach($chapters as $chapter)
                <option value='{{$chapter->id}}'>{{$chapter->title}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group"><br />
            <button tabindex="3" type="submit" class="btn btn-primary create-chapter">Create Chapter</button>
        </div>
    </fieldset>
</form>