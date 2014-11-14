<?php

class PagesController extends \BaseController {
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
//                  $new_submissions = UserManager::new_submissions(5);
//                  $unattended_submissions = UserManager::unattended_submissions(5);
//                  $new_comments = UserManager::new_admin_comments(5);
//                  $unattended_comments = UserManager::unattented_comments(5);
//                  return View::make('pages.admin_dash')
//                          ->with('pageTitle','Welcome')
//                          ->withNew_submissions($new_submissions)
//                          ->withUnattended_submissions($unattended_submissions)
//                          ->withNew_comments($new_comments)
//                          ->withUnattended_comments($unattended_comments);
              }
              else{
                  $comments = UserManager::new_comments(Auth::user()->id);
                  $remarks = UserManager::new_remarks(Auth::user()->id);
                  $notifications = UserManager::compile_notifications($comments, $remarks);
                  $courses = Lesson::user_courses(true);
                  $visited = Auth::user()->lessons;
                  $plans = PaymentPlan::get();
                  $expired = DB::table('programs_users')->where('user_id',Auth::user()->id)->count();
                  if($visited=='') $visited = array();
                  else $visited = json_decode($visited, true);
                  $meta['header_img_text'] = sys_settings('domain');
                  return View::make('pages.user_dash')->with('pageTitle','Welcome')->withComments($comments)->withVisited($visited)
                          ->withRemarks($remarks)->withNotifications($notifications)->withMeta($meta)->withCourses($courses)
                          ->withPlans($plans)->withExpired($expired);
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
            return Mailer::contact(Input::all());
        }
        
        public function jsconfig(){
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
        
        public function save_report_image(){
            $data = $_POST['data'];
            $file = sys_settings() .'-report-'.$_POST['id']. '.png';
            // remove "data:image/png;base64,"
            $uri =  substr($data,strpos($data,",")+1);
            // save to file
            file_put_contents(base_path().'/assets/uploads/reports/'.$file, base64_decode($uri));
            // return the filename
            return $file;
        }
        
        public function print_report($file, $render=false){
           if($render=='render') return  View::make("reports.print")->withFile($file);
           return Response::download(base_path()."/assets/uploads/reports/$file");
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
        
}
