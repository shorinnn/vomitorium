@extends($layout)

@section('content')
        <div class="container">
            @if($data['chapters']->count()==0)
            <p class='text-center'>Add your first module</p><br />
            <p class='tip' style='text-align:center'>Tip: A module is like a chapter, week, or just ... module of your training/product/anything you wish.</p>
            @endif
            <button class='btn btn-default new-module'><i class='glyphicon glyphicon-plus'></i> Add Module</button>
                <div class="table-responsive table-modules sortablex">
                    
                    {{View::make('modules.modules')->withData($data)}}
                </div>
            <div class='new-module-form pull-left'>
                <form class='add-module' action='{{route('modules.store')}}'>
                    <input type='text' class='form-control' id='module-name' name='module-name' placeholder="Enter module title"/><br />
                    <div class="lesson-count" style="vertical-align: middle">
                        <input type='text' class='form-control' id='lesson-count' name='lesson-count' value="1"s />
                        Lesson(s) 
                        <span>You can always create more later!</span>
                    </div>
                    <br />
                    <select class="form-control" id="position" name="position">
                    </select>
                    <br />
                    <div class="text-center">
                        <button class='btn btn-success'>Add</button>
                    </div>
                </form>

            </div>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    <div style="display:none">
        <img src="{{url('assets/img/ajax-loader-transparent.gif')}}" />
    </div>
@stop

<script>
   var evt = {event:'click',target:'body', callback:'set_unload_warning'};
   var evt2 = {event:'hidden',target:'editable', callback:'set_unload_warning'};
   var registered_listeners = [evt, evt2];    
</script>