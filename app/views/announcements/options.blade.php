<p class='msg green'>Ok, you're set!</p>
<br />

<p class='tip'>
<i>Tidbit</i>: 
<img style="height:14px" src="{{url('assets/img/TrainingAutomate_logo.png')}}" /> are 
<b>PDFs on steroids, training programs of the future</b> or <b>products 3.0</b>, with no extra effort needed on your part.
It’s just as easy, if not <u>easier</u> than anything else.
<br />
<br />
This is what technology is for, right? Making things better.
</p>
<br />

<div class='text-center program-options'>
    What do you want to do first?<br />
    <a href='{{url('modules')}}'>Create my program outline</a>
    <div id='add_form_a' onclick='create_lesson_form()'>Create my first lesson
        <div class="add_form">
            <form method="POST" action="{{action("LessonsController@store")}}" accept-charset="UTF-8" id="add_form">
                <fieldset>
                    <p class='red'>Step 1:</p>
                    <p class='msg'>Name Your Lesson</p>
                    <div class="form-group">
                        <input autofocus="autofocus" class="form-control" tabindex="1" 
                               placeholder="e.g. Introduction to dancing" type="text" name="title" id="title" required>
                    </div>
                    <div class="form-group">
                        <button tabindex="3" type="submit" class="btn btn-default">Create Lesson</button>
                    </div>
                </fieldset>
            </form>
            </div>
    </div>
    <a href='#'>Create product delivery page</a>
</div>