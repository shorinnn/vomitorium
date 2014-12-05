<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()){
            if(Request::ajax()){
                return "<script>window.location = '".url('/')."';</script>";
            }
            return Redirect::guest('login');
        }
});

Route::filter('admin',function(){
    if(!admin()){
        if(Request::ajax()){
            return "<script>window.location = '".url('login')."';</script>";
        }
       return Redirect::guest('login');
   }
});

Route::filter('program',function(){
    $has_program = true;
    if(!Session::has('program_id'))  $has_program = false;
    $program = Program::find(Session::get('program_id'));
    if($program==null)   $has_program = false;
    if(!$has_program){
        if(Request::ajax()){
            return "<script>window.location = '".url('/')."';</script>";
        }
       return Redirect::to('/');
   }
});

Route::filter('saas-admin',function(){
    $whitelist = ['115.66.142.54', '94.52.185.22'];
    if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) return Redirect::to('/');
    if(Session::has('subdomain') && Session::get('subdomain')!='www'){
        
        return Redirect::to('/');
    }
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

