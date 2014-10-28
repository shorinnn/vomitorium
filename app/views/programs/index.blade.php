@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            <button type="button" class="btn btn-primary create-form-btn btn-default" onclick="create_form()">Add New</button>
            <div class="add_form">
            <form method="POST" action="{{action("ProgramsController@store")}}" accept-charset="UTF-8" id="create_form">
                <input type="hidden" name="just_program" value="1" />
                <fieldset>
                    <div class="form-group">
                        <label for="title">What is the Program Name?</label>
                        <input autofocus="autofocus" class="form-control" tabindex="1" placeholder="e.g. Another Awesome Program" type="text" name="program" id="program" required>
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
                    <tr class="list-row"><th>Program Name</th><th>Registered Users</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($programs as $p)
                    <tr class='list-row list-row-{{$p->id}}'><td>
                            <a class="editable" href="#" id="name" data-type="text" data-pk="{{$p->id}}" 
               data-name="name" data-url="{{url('programs/update')}}" data-original-title="Enter program name" data-mode='inline'>
                                {{$p->name}}
                            </a>
                        </td>
                        <td>{{$p->users->count()}}</td>
                        <td>
                            <button class='btn btn-primary' onclick='choose_program_id({{$p->id}})'>Select</button>
                            <button class='btn btn-danger' onclick='del({{$p->id}}, "{{url('programs/'.$p->id)}}")'>Delete</button>
                        </td>
                    </tr>
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