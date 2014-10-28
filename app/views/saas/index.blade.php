@extends('layouts.saas')

@section('content')
   
<h1>Vomitorium Users</h1>
<h2>Create Account</h2>
<form method='post' action='{{route('accounts.store')}}' id='account_form' class='nodisplay'>
    Admin Email<br />
    <input type='text' class='form-control' placeholder='Admin Email' id='admin_email' name='admin_email' /><br />
    Subdomain<br />
    <input type='text' class='form-control' onkeyup='prepopulate_fields()' placeholder='Subdomain' id='subdomain' name='subdomain' /><br />
    DB Name<br />
    <input type='text' class='form-control' placeholder='DB Name' name='db_name' id='db_name' /><br />
    DB UserName<br />
    <input type='text' class='form-control' placeholder='DB UserName' name='db_username' id='db_username' /><br />
    DB Pass<br />
    <input type='text' class='form-control' placeholder='DB Pass' name='db_pass' id='db_pass' /><br />
    
    <button class='btn btn-primary'>Create Account</button>
    <br />
</form><br />
<button class="btn btn-primary" onclick="$('#account_form').slideToggle();">Add Account</button>
<h2>Existing Users</h2>
<table class='table table-striped table-bordered'>
    <tr><td>ID</td><td>Domain</td><td>DB Name</td><td>DB UserName</td><td>DB Pass</td><td>Actions</td></tr>
    @foreach($accounts as $a)
        {{View::make('saas._account')->withA($a)}}
    @endforeach
</table>

@stop