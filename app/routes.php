<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
// Determine subdomain and set in session
$_SERVER['SERVER_NAME'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
if(App::environment()=='production') $domain = 'imacoa';
else $domain = 'vomitorium';

if (preg_match('/^(www|\/).*/',$_SERVER['SERVER_NAME']) || preg_match("/^($domain|\/).*/",$_SERVER['SERVER_NAME'])) {
   // Public site, no subdomain
    Session::forget('subdomain');
} else {
  // Subdomain.  
  $subdomain = preg_replace('/^([^\.]+).*/i', '$1', $_SERVER['SERVER_NAME']);
  // Set subdomain in session.
  Session::set('subdomain',$subdomain);
  //die('has subdomain'.$subdomain);
}

 Route::resource('jsconfig', 'PagesController@jsconfig');
 
if (!Session::has('subdomain')) {
  // The user is not logged in.  We are on the public facing site.
    Route::get('accounts/admin/{id}','AccountsController@admin');
    Route::post('accounts/admin/{id}','AccountsController@update_admin');
    Route::get('accounts/status/{id}/{status}','AccountsController@status');
    Route::get('accounts/db_change','AccountsController@db_change');
    Route::post('accounts/db_change','AccountsController@do_db_change');
    Route::get('accounts/lesson_notifications','AccountsController@lesson_notifications');
    Route::resource('accounts', 'AccountsController');
    Route::get("/", function() {
        echo "<div style='text-align:center'><h1 style='text-align:center'>MAIN SITE</div>"; 
    });
} else {
  // The user is attempting to access a domain.
  Route::group(array("before"=>"switchToTenantDB"), function() {
        $dbdata = DB::table('accounts')->where('subdomain', Session::get('subdomain'))->first();
        if($dbdata==null) die('This account doesn\'t exist yet');
        
        Config::set('database.connections.mysql_tennant.database', $dbdata->db_name);
        Config::set('database.connections.mysql_tennant.username', $dbdata->db_username);
        Config::set('database.connections.mysql_tennant.password', $dbdata->db_pass);
        Config::set('app.url', 'http://'.Session::get('subdomain').'.'.Config::get('app.base_url'));
        DB::setDefaultConnection('mysql_tennant');

   
        View::share('layout', Session::has('style') ? 'layouts.'.Session::get('style') : 'layouts.master');
        
        Route::post('infusionsoft/callback', 'PagesController@infusionsoft');
        Route::post('stripe_hook', 'PagesController@stripe_hook');
        Route::post('paypal_ipn', 'PagesController@paypal_ipn');
        Route::post('thank_you', 'PagesController@thank_you');
        Route::get('paypal_check', 'PagesController@paypal_check');
        Route::get('payment', 'PagesController@payment');
        Route::post('purchase', 'PagesController@purchase');
        Route::post( 'pm_search',          'PMController@search');
        Route::get( 'go_to/{id}/{segment}/{hash}',          'PMController@go_to');
        Route::get( 'go_to/{id}/{segment}',          'PMController@go_to');
        Route::get( 'go_to/{id}',          'PMController@go_to');
        Route::post( 'load_convo/{id}',          'PMController@load_convo');
        Route::get( 'load_convo/{id}',          'PMController@load_convo');
        Route::get( 'inbox/{search}',          'PMController@index');
        Route::get( 'inbox',          'PMController@index');
        Route::post( 'inbox',          'PMController@store');
        Route::get( 'change_style',          'PagesController@change_style');
        Route::post( 'reports/save_report_image',          'PagesController@save_report_image');
        Route::get( 'reports/print/{file}',          'PagesController@print_report');
        Route::get( 'reports/print/{file}/{render}',          'PagesController@print_report');
        Route::get( 'reports/print_loading',          'PagesController@print_loading');
        Route::get( 'reports/{slug}',          'PagesController@reports');
        Route::get( 'conversation/{lesson_id}',          'CourseController@conversation');
        Route::get( 'conversation/{lesson_id}/{user_id}',          'CourseController@conversation');

        Route::get( 'lessons/view_lesson/{id}',          'LessonsController@view_lesson');
        Route::post( 'lessons/intro/{id}',          'LessonsController@change_intro');
        Route::get( 'lessons/deadline_notifications/{id}',          'LessonsController@deadline_notifications');
        Route::get( 'load_messages',          'CourseController@load_messages');
        Route::get( 'load_lesson_comments',          'CourseController@load_lesson_comments');
        Route::get( 'dynamic_answers/{cat_id}',          'CourseController@dynamic_answers');
        Route::post( 'mark_read',          'CourseController@mark_read');
        Route::post( 'mark_remark_read',          'CourseController@mark_remark_read');
        Route::post( 'mark_remark_attended',          'AdminController@mark_remark_attended');
        Route::post( 'post_remark',          'AdminController@post_remark');
        Route::post( 'edit_remark',          'AdminController@edit_remark');
        Route::post( 'mark_attended',          'AdminController@mark_attended');
        Route::post( 'mark_unattended',          'AdminController@mark_unattended');
        Route::post( 'mark_submission_attended',          'AdminController@mark_submission_attended');
        Route::post( 'mark_submission_unattended',          'AdminController@mark_submission_unattended');
        Route::post( 'mark_lesson',          'AdminController@mark_lesson');
        Route::post( 'mark_user',          'AdminController@mark_user');
        Route::post( 'reply',          'CourseController@reply');
        Route::post( 'remark_reply',          'CourseController@remark_reply');
        Route::post( 'group_reply',          'CourseController@group_reply');
        Route::post( 'edit_reply',          'CourseController@edit_reply');
        Route::post( 'courses/attach',          'CourseController@attach');
        Route::post( 'courses/delete_attachment',          'CourseController@delete_attachment');
        Route::post( 'courses/save_image',          'CourseController@save_image');
        Route::post( 'courses/save_file',          'CourseController@save_file');
        Route::get( 'courses',          'CourseController@courses');
        Route::get( 'new_submissions',          'AdminController@new_submissions');
        Route::get( 'unattended_submissions',          'AdminController@unattended_submissions');
        Route::get( 'new_messages',          'AdminController@new_messages');
        Route::get( 'unattended_messages',          'AdminController@unattended_messages');
        Route::get( 'userpage/{id}',          'AdminController@user_page');

        Route::get('skills', 'SkillsController@index');
        Route::post( '/skills/update',          'SkillsController@update');

        Route::post('programs/choose/{id}', 'ProgramsController@choose');
        Route::post('programs/update', 'ProgramsController@update');
        Route::resource('programs', 'ProgramsController');
        Route::resource('lesson_alerts', 'LessonAlertsController');
        Route::post('payment_plans/update_processor', 'PaymentPlansController@update_processor');
        Route::delete('payment_plans/delete_processor/{id}', 'PaymentPlansController@delete_processor');
        Route::post('payment_plans/processor', 'PaymentPlansController@processor');
        Route::get('payment_plans/processors', 'PaymentPlansController@processors');
        
        Route::resource('payment_plans', 'PaymentPlansController');
        Route::post('announcements/update', 'AnnouncementsController@update');
        Route::resource('announcements', 'AnnouncementsController');
        Route::post('modules/move_lesson', 'ModulesController@move_lesson');
        Route::post('modules/move_chapter', 'ModulesController@move_chapter');
        Route::post('modules/store_lesson', 'ModulesController@store_lesson');
        Route::resource('modules', 'ModulesController');
        Route::resource('chapters', 'ChaptersController');
        Route::post( '/chapters/update',          'ChaptersController@update');
        Route::get( 'chapters/move/{direction}/{id}',          'ChaptersController@move');
        Route::get( 'lessons/create_chapter/{lesson}',          'LessonsController@create_chapter');
        Route::get( 'lessons/all_chapters',          'LessonsController@all_chapters');
        Route::post( 'lessons/store_chapter',          'LessonsController@store_chapter');
        Route::get( 'lessons/get_answers/',          'LessonsController@get_answers');
        Route::get( 'lessons/get_answers/{is_report}',          'LessonsController@get_answers');
        Route::resource('lessons', 'LessonsController');
        Route::post( 'lessons/add_category',          'LessonsController@add_category');
        Route::post( 'lessons/{lesson}',          'LessonsController@update');
        Route::post( 'lessons/blocks/move_block_to_pos',          'LessonsController@move_block_to_pos');
        Route::get( 'lessons/{lesson}/move/{direction}',          'LessonsController@move');
        Route::get( 'lessons/{lesson}/editor',          'LessonsController@editor');

        Route::post( 'lessons/add_block/{lesson}',          'LessonsController@add_block');
        Route::delete( 'lessons/remove_block/{block}',          'LessonsController@remove_block');
        Route::post('lessons/move_block/{block}/{direction}',          'LessonsController@move_block');
        Route::post('lessons/update_block/{block}',          'LessonsController@update_block');
        Route::post('lessons/block/saveimage',          'LessonsController@save_image');
        Route::post('lessons/block/save_file',          'LessonsController@save_file');
        Route::post( '/categpries/update',          'CategoriesController@update');
        Route::resource('categories', 'CategoriesController');
        Route::get( 'reports',          'ReportsController@index');
        Route::post( 'reports/create',          'ReportsController@create');
        Route::delete( 'reports/delete/{id}',          'ReportsController@destroy');
       



        //Route::post( '/chapters/{chapter}/lessons/{lesson}',          'LessonsController@update');
        //Route::get( 'chapters/{chapter}/lessons/{lesson}/move/{direction}',          'LessonsController@move');
        //Route::get( 'chapters/{chapter}/lessons/{lesson}/editor',          'LessonsController@editor');
        //Route::post( 'chapters/add_block/{lesson}',          'LessonsController@add_block');
        //Route::delete( 'chapters/remove_block/{block}',          'LessonsController@remove_block');
        //Route::post('chapters/move_block/{block}/{direction}',          'LessonsController@move_block');
        //Route::post('chapters/update_block/{block}',          'LessonsController@update_block');
        //Route::post('chapters/block/saveimage',          'LessonsController@save_image');


        Route::resource('/', 'PagesController');// Confide routes
       
        Route::post('search_users', 'PagesController@search_users');// Confide routes
        Route::get( '/register',                 'UserController@create');
        Route::get( '/register/{hash}',                 'UserController@create');
        Route::get( '/register/accesspass/{hash}',                 'UserController@access_pass');
        Route::get( '/access-code',                 'UserController@access_code');
        Route::post( '/access-code',                 'UserController@access_code');
        Route::post('user',                        'UserController@store');
        Route::get( '/login',                  'UserController@login');
        Route::post('/login',                  'UserController@do_login');
        Route::get( '/confirm/{code}',         'UserController@confirm');
        Route::get( '/forgot_password',        'UserController@forgot_password');
        Route::post('/forgot_password',        'UserController@do_forgot_password');
        Route::get( '/reset_password/{token}', 'UserController@reset_password');
        Route::post('/reset_password',         'UserController@do_reset_password');
        Route::get( '/logout',                 'UserController@logout');
        Route::get( '/users/add_clients_ui',          'UserManagerController@add_clients_ui');
        Route::get( '/users/chat_permissions/{id}',          'UserManagerController@chat_permissions');
        Route::post( '/users/chat_permissions/{id}',          'UserManagerController@set_chat_permissions');
        Route::get( '/users/assign_coach/{id}',          'UserManagerController@assign_coach');
        Route::post( '/users/assign_coach/{id}',          'UserManagerController@do_assign_coach');
        Route::post( '/users/register',          'UserManagerController@register');
        Route::get( '/users/comma_separated_codes',          'UserManagerController@comma_separated_codes');
        Route::get( '/users/codes',          'UserManagerController@codes');
        Route::get( '/users',                 'UserManagerController@index');
        Route::post( '/users/update',          'UserManagerController@update');
        Route::delete( 'users/{id}',          'UserManagerController@destroy');
        Route::get( 'users/{id}/change_password',          'UserManagerController@change_password');
        Route::post( 'users/change_password',          'UserManagerController@do_change_password');
        Route::post( 'users/search',          'UserManagerController@search');

        Route::get( '/about-us/video-introduction',                 'PagesController@page');
        Route::get( 'easteregg',                 'PagesController@easteregg');
        Route::get( '/who-we-are/{any}',                 'PagesController@page');
        Route::get( '/the-brilliant-career',                 'PagesController@page');
        Route::get( '/the-brilliant-career/{any}',                 'PagesController@page');
        Route::get( '/resources/{any}',                 'PagesController@page');
        Route::get( '/contact-us',                 'PagesController@contact');
        Route::post( '/contact-us',                 'PagesController@do_contact');
        Route::get( 'lesson/{slug}',                 'CourseController@lesson');
        Route::get( 'lesson/{slug}/{user_id}',                 'CourseController@lesson');
        Route::post( 'lesson/{slug}',                 'CourseController@store_answer');
        Route::post( 'subscriptions',                 'UserController@cancel_subscription');
        Route::get( 'subscriptions',                 'UserController@subscriptions');
        Route::get( 'settings',                 'UserController@settings');
        Route::post( 'settings',                 'UserController@upload_avatar');
        Route::get( 'system_settings',          'AdminController@system_settings');
        Route::get( 'appearance',          'AdminController@appearance');
        Route::post( 'system_settings',          'AdminController@update_system_settings');
        Route::post( 'background',          'AdminController@background');
        Route::post( 'logo',          'AdminController@logo');
  });
}