@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            <a href='{{url('/')}}'>&lt; Back To Dashboard</a>
            <br />
            <br class='clearfix clear_fix' />
            <button type="button" class="btn btn-primary create-form-btn btn-default pull-left" onclick="create_form()">New Message</button>
            <div class="col-lg-5 pull-right text-right">
                <input type="text" class="form-control inbox-search" placeholder="Search" onkeyup='delay(search_inbox,300)'/>
            </div>
            <div class='col-lg-2 searching-placeholder text-right pull-right'>
                searching... <img src='{{url('/assets/img/ajax-loader.gif')}}' /> </div>
            <span class="clearfix clear_fix"></span>
            <div class="add_form">
            <form method="POST" action="{{action("PMController@store")}}" accept-charset="UTF-8" class='ajax-form' id='pm-form'>
                <div class='row'>
                    <div class="col-lg-12 form-group">
                        <label>To</label>
                        <select name='to' id='to' data-toggle='combobox' class='form-control'>
                            <option></option>
                            @foreach($recipients as $r)
                            <option value='{{$r->id}}'>{{$r->username}} ({{$r->first_name}} {{$r->last_name}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class="col-lg-12 form-group">
                        <label>Message</label>
                        <textarea id='message' name='message'  class="white-textarea summernote_editor"></textarea>
                    </div>
                </div>
                <button class='btn btn-default' data-submit='1'>Send</button>
            </form>
            </div>
            <br />
            <br />
            <div class='search-results convo'></div>
            <div class='inbox-area'>
                <div style="margin-bottom:15px">
                        {{Form::select('filter',
                                    array('all'=>'Show All','pm'=>'Private Messages','question'=>'Questions','lesson'=>'Lessons'),
                                    Input::get('filter'),
                                    array('class'=>'form-control convo-filter'))
                        }}
                </div>
                <div id='ajax-content' class='convo'>
                        @if($convo->count()>0)
                            {{View::make('pm.conversations')->withConvo($convo)}}
                        @else
                            <p class='no-messages'>You have no messages.</p>
                        @endif
                </div>
            </div>
        </div>
        <!-- /.container -->
       
    </div>
    <!-- /.section -->
 <script>
        var do_enable_rte = true;
        var rte_config = 3;
        @if($search!='')
            search_term = 'pmid--{{$search}}';
            var onload_functions = ['search_inbox'];
        @endif
    </script>
@stop