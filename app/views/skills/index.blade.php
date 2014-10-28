@extends($layout)

@section('content')
<div class="section">
        <div class="container">
            
             <div class="table-responsive">
                <table class="table table-bordered table-striped" style="max-width: 900px; width:900px">
                    <thead>
                        <tr><th>Skill Type</th><th>Options</th></tr>
                    </thead>
                    <tbody>
                        @foreach($skills as $skill)
                            <tr><td>{{$skill->type}}</td><td>
                                    <a class="editable" href="#" id="value" data-type="textarea" data-pk="{{$skill->id}}" 
                           data-name="value" data-url="{{action("SkillsController@update")}}" data-original-title="Enter Skill Options" data-mode='inline'>{{$skill->values}}
                        </a>
                                    </td></tr>
                        @endforeach
                    </tbody>
                    </table>
             </div>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop