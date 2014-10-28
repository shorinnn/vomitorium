@extends($layout)

@section('content')
        <div class="container">
           <button class="btn btn-default inline" onclick="add_report_tag(1)">View Tags</button> 
           <button class="btn btn-default inline" onclick="create_report()">Create Report</button> 
           <br />
           <br />
           <table class="table table-bordered table-striped">
               @foreach($reports as $r)
               <tr class='list-row list-row-{{$r->id}}'><td>{{$r->title}}</td><td><a href='{{url('reports/'.$r->slug)}}' target='_blank'>View Report</a></td>
                   <td><button onclick='del({{$r->id}},"{{action("ReportsController@destroy", array($r->id))}}")' class="btn btn-danger">Delete</button></td></tr>
               @endforeach
           </table>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop