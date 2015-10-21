<?php

class PagesController extends \BaseController {
       public function __construct(){
//            $this->beforeFilter('auth', array('only' => 'contact'));
       }
       
       public $meta;
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{$t = new Paypal_transaction();
          $meta['header_img_text'] = 'Welcome';
          if(Auth::guest()){
                $meta['header_img_text'] = 'Welcome';
                if(sys_settings('installation')=='31-1408525988') unset( $meta['header_img_text'] );
		return View::make('pages.index')->with('pageTitle','Welcome')->withMeta($meta);
          }
          else{
              if(admin()){
                  if(Request::ajax()){
                      $current_program = Program::find(Session::get('program_id'));
                      if(Input::get('collection')=='newest'){
                          $newest = $current_program->newest_users();
                          return View::make('admin.newest_clients')->withNewest($newest);
                      }
                      if(Input::get('collection')=='unattended'){
                          $unattended = UserManager::unattended_users();
                          return View::make('admin.userlist')->withUnattended($unattended);
                      }
                      if(Input::get('collection')=='submissions'){
                          $new_submissions = UserManager::new_submissions();
                          return View::make('admin.admin_submissions_partial')->withSubmissions($new_submissions);
                      }
                      if(Input::get('collection')=='private_messages'){
                          $pm = PMController::unread_pm();
                          return View::make('admin.private_messages')->withPm($pm);
                      }
                  }
  
                  $unattended = UserManager::unattended_users();
                  $newest = array();
                  $current_program = $new_submissions = null;
                  if(Session::has('program_id')){
                      $current_program = Program::find(Session::get('program_id'));
                      $newest = $current_program->newest_users();
                      $new_submissions = UserManager::new_submissions();
                  }
                  $meta['pageTitle'] = 'Welcome '.Auth::user()->username;
                  $meta['javascripts'] = array('../assets/js/admin/lessons.js');
                  $template = 'template_3';
                  $pm = PMController::unread_pm();
                       
                  return View::make('admin.dash.base')->withMeta($meta)->withUnattended($unattended)->withCurrent_program($current_program)
                          ->withNewest($newest)->withNew_submissions($new_submissions)->withPm($pm);

              }
              else{
                  $comments = UserManager::new_comments(Auth::user()->id);
                  $remarks = UserManager::new_remarks(Auth::user()->id);
                  $notifications = UserManager::compile_notifications($comments, $remarks);
                  $courses = Lesson::user_courses(true);
                  $visited = Auth::user()->lessons;
                  $plans = PaymentPlan::get();
                  $plan_id = DB::table('programs_users')->where('user_id', Auth::user()->id)
                          ->where('expires', '<', date('Y-m-d H:i:s') )
                          ->orderBy('expires','DESC')->limit(1)->first();
                  if($plan_id==null) $plans = PaymentPlan::get();
                  else $plans = PaymentPlan::where('id', $plan_id->subscription_id)->get();
                  $expired = DB::table('programs_users')->where('user_id',Auth::user()->id)->count();
                  if($visited=='') $visited = array();
                  else $visited = json_decode($visited, true);
                  $meta['header_img_text'] = sys_settings('domain');
                  if(Session::has('program_id')){
                      $meta['header_img_text'] = Program::find( Session::get('program_id') )->name;
                  }
                  $intro_content = '';
                  
                  $intro_lesson = Lesson::where('program_id', Session::get('program_id'))->where('intro_lesson', 1)->first();
                  if($intro_lesson !=null){
                      $intro_content = View::make('pages.intro_lesson')->withLesson($intro_lesson);
                  }
                  return View::make('pages.user_dash')->with('pageTitle','Welcome')->withComments($comments)->withVisited($visited)
                          ->withRemarks($remarks)->withNotifications($notifications)->withMeta($meta)->withCourses($courses)
                          ->withPlans($plans)->withExpired($expired)->withIntro_content($intro_content);
              }
          }
	}
        
        public function search_users(){
            $users = User::where('username', 'like', Input::get('search').'%')->paginate(15);     
            //return View::make('admin.users')->withUsers($users);
            return View::make('admin.userlist')->withUnattended($users);
        }
        
        
        public function page($any=''){
            $view = rtrim(implode('-', array(Request::segment(1),Request::segment(2))),'-');
            return View::make("pages.static.$view");
        }
        
        
        
        public function contact(){      
            $this->meta['pageTitle'] = 'Support';
            $this->meta['header_img_text'] = 'Support';
            return View::make('pages.contact')->withMeta($this->meta);
        }
        
        public function do_contact(){ 
//            $_POST['contact_name'] = Auth::user()->first_name.' '.Auth::user()->last_name;
//            $_POST['contact_email'] = Auth::user()->email;
            $_POST['contact_message'] = Input::get('contact_message');
            $_POST['contact_subject'] = Input::get('contact_subject');
            return Mailer::contact($_POST);
        }
        
        public function jsconfig(){
//            return View::make('pages.jsconfig');
            return Response::make(View::make('pages.jsconfig'), 200, array('Content-Type' => 'application/javascript'));
        }
        
        public function change_style(){
            if(Session::has('style')){
                if(Session::get('style')=='master') Session::put('style','master-backup');
                else Session::put('style','master');
            }
            else Session::put('style','master2');
            return Redirect::back();
        }
        
        public function reports($slug){
            
            $report = Report::where('slug', $slug)->first();
            if($report==null) return "Report unavailable";
            $content = parse_tags($report->content, null, $report->title);
            return  View::make("reports.show")->withContent($content)->withReport($report);
        }
        
        public function downloadCookie(){
            $cookie = Cookie::get('fileDownloadToken'); 
            Cookie::queue('fileDownloadToken', 0, 0);
            return $cookie;
        }
        
        public function save_report_image(){
            $data = $_POST['data'];
            $file = sys_settings() .'-report-'.$_POST['id']. '.png';
            $file = sys_settings() .'-report-'.$_POST['id']. '.jpg';
            // remove "data:image/png;base64,"
            $uri =  substr($data,strpos($data,",")+1);
            // save to file
            file_put_contents(base_path().'/assets/uploads/reports/'.$file, base64_decode($uri));
            // return the filename
            return $file;
        }
        
        public function pre_print_report($file = '', $render = '', $token =''){
            return 'Generating report - please wait... (this window will auto-close on completion)';
        }
        
        public function print_report($file, $render=false, $token = ''){
           $cookie = Cookie::queue('fileDownloadToken', 1, 1);
           if($render=='render') return  View::make("reports.print")->withFile($file);
           return Response::download( base_path()."/assets/uploads/reports/$file" );
        }
        public function print_loading(){
            return  "<br /><br /><br /><br /><center>".HTML::image('assets/img/ajax-loader.gif')."<h1>Generating Report - please wait...</h3></center>";
        }
        
         
        function purchase(){
            $meta['header_img_text'] = 'Purchase';
            return Stripe_processor::purchase($meta);
        }
        
        
        public function payment(){
              $hash = Session::get('payment_plan_id');
              if(strpos($hash, '-trial')!=false){
                  Session::set('trial',1);
                  $hash = str_replace('-trial', '', $hash);
              }
              $plans = PaymentPlan::whereRaw('SHA1(CONCAT("'.sys_settings().'",id)) = "'.$hash.'"')->get();
              $meta['header_img_text'] = 'Payment';
              return View::make('payment_plans.payment')->with('pageTitle','Payment')->withMeta($meta)->withPlans($plans);
        }
        
        public function stripe_hook(){
            Return Stripe_processor::process_hook();
        }
        
        public function paypal_ipn(){
            Return Paypal_processor::ipn();
        }
        
        public function thank_you(){
            $custom = json_decode(urldecode($_POST['custom']), true);
            $plan = PaymentPlan::find($custom['p']);
            Session::set('program_id', $plan->program_id);
            Session::forget('trial');
            $meta['header_img_text'] = 'Purchase Complete';
            $plans = PaymentPlan::where('program_id',$custom['p'])->get();
            
            $_POST['first_name'] = Auth::user()->first_name;
            $_POST['last_name'] = Auth::user()->last_name;
            $_POST['email'] = Auth::user()->email;
            $_POST['program'] = Program::find( Session::get('program_id') );
            Mailer::program_purchased($_POST);
            
            
            Return View::make('payment_plans.paypal_success')->withMeta($meta);
        }
        
        public function paypal_check(){
            if (Auth::user()->programs()===false) return 0;
            return 1;
        }
        
        public function infusionsoft(){
            $str = Input::all();
            $str = print_r($str, true);
            mail('shorinnn@yahoo.com','is', $str);
        }
        
        public function about_us(){
            $meta['header_img_text'] = 'About Us';
            $content = sys_settings('about_us_html');
            Return View::make('pages.basic_page')->withMeta($meta)->with( compact('content') );
        }
        
}
