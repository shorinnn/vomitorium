@extends('layouts.saas')

@section('content')
   
<h1>DB Change</h1>
@if ( Session::get('error') )
    <div class="alert alert-error alert-warning">{{{ Session::get('error') }}}</div>
@endif

@if ( Session::get('notice') )
    <div class="alert alert-success">{{{ Session::get('notice') }}}</div>
@endif
<p class='alert alert-info'>NOTE: Table names must use the <b>&lt;THEDATABASE&gt;</b> prefix (eg: DROP THEDATABASE.`custom_table`)</p>
SQL Changes:<br />
<form method='post' action='{{url('accounts/db_change')}}'>
    <textarea  name='sql' class='form-control' rows="6"></textarea><br />
    <button class='btn btn-default'>Run SQL</button>
</form>
@stop