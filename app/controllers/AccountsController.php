<?php

class AccountsController extends BaseController {

    public function __construct() {
        $this->beforeFilter('saas-admin');
    }

    public function index(){
        return View::make('saas.index')->withAccounts(Account::orderBy('id','ASC')->get());
    }

    public function db_change(){
        return View::make('saas.db_change');
    }

    public function lesson_notifications(){
        $today = date("Y-m-d H:i:s");
        // update customer DB
        $accounts = array();
        $days = array();
        foreach(Account::all() as $a){
            $db = $a->db_name;
            Config::set('mail.from.address', DB::table($db.'.settings')->first()->email_from);
            Config::set('mail.from.name', DB::table($db.'.settings')->first()->email_name);

            // get todays lessons
            $today = date('m/d/Y');
            $lessons = DB::table($db.'.lessons')->where('release_type', 'on_date')->where('release_value', $today)->where('release_email',1)->get();
            // loop through lessons
            foreach($lessons as $l){
                // get clients linked to this lesson
                $notified = DB::table($db.'.lesson_notifications')->where('lesson_id', $l->id)->lists('user_id');
                if(count($notified)==0) $notified = array(0);
                $clients = DB::table($db.'.programs_users')->where('program_id', $l->program_id)->whereNotIn('user_id', $notified)->lists('user_id');
                if(count($clients)==0) $clients = array(0);
                $clients = DB::table($db.'.users')->whereIn('id', $clients)->get();
                foreach($clients as $c){
                    // notify client
                    $accounts[$a->subdomain][$l->title][] = $c;
                    $data['text'] = $l->release_email_content;
                    Mail::send('emails.lesson_notification', $data, function($message) use ($c){
                        $message->to($c->email, "$c->first_name $c->last_name")->subject('New Lesson Available!');
                    });
                    $data = array('lesson_id' => $l->id, 'user_id' => $c->id, 'timestamp' => date('Y-m-d H:i:s'));
                    DB::table($db.'.lesson_notifications')->insert($data);
                }
            }
            // get day based lessons
            $lessons = DB::table($db.'.lessons')->where('release_type', 'after')->where('release_email',1)->get();
            // loop through lessons
            foreach($lessons as $l){
                // get clients linked to this lesson
                $notified = DB::table($db.'.lesson_notifications')->where('lesson_id', $l->id)->lists('user_id');
                if(count($notified)==0) $notified = array(0);
                $date = date('Y-m-d 00:00:00', strtotime("$today - $l->release_value day"));
                $clients = DB::table($db.'.programs_users')
                        ->where('program_id', $l->program_id)
                        ->whereNotIn('user_id', $notified)
                        ->whereNotNull('start_date')
                        ->where('start_date','<=',$date)
                        ->lists('user_id');
                if(count($clients)==0) $clients = array(0);
                $clients = DB::table($db.'.users')->whereIn('id', $clients)->get();
                foreach($clients as $c){
                    // notify client
                    $days[$a->subdomain][$l->title][] = $c;
                    $data['text'] = $l->release_email_content;
                    Mail::send('emails.lesson_notification', $data, function($message) use ($c){
                        $message->to($c->email, "$c->first_name $c->last_name")->subject('New Lesson Available!');
                    });
                    $data = array('lesson_id' => $l->id, 'user_id' => $c->id, 'timestamp' => date('Y-m-d H:i:s'));
                    DB::table($db.'.lesson_notifications')->insert($data);
                }
            }

        }
        $accounts['date'] = $accounts;
        $accounts['days'] = $days;
        return View::make('saas.lesson_notifications')->withAccounts($accounts);
    }

    public function do_db_change(){
        if(!Input::has('sql') || Input::get('sql')=='') {
            Session::flash('error',"Please submit valid SQL code.");
            return View::make('saas.db_change');
        }
        $sql = explode(';', Input::get('sql'));

        // update the template DB
        $database = "`saas_template`";
        foreach($sql as $statement){
            if($statement=='') continue;
            $statement = str_replace('THEDATABASE', $database, $statement);
            DB::connection('mysql')->statement($statement);
        }

        // update customer DB
        foreach(Account::all() as $a){
            $database = $a->db_name;
            foreach($sql as $statement){
                if($statement=='') continue;
                $statement = str_replace('THEDATABASE', $database, $statement);
                DB::connection('mysql')->statement($statement);
            }
        }

        Session::flash('notice',"Update Successful");
        return View::make('saas.db_change');
    }

    public function destroy($id){
        $acc = Account::find($id);
        // delete MySQL DB
        DB::connection('mysql')->statement("DROP DATABASE `$acc->db_name`");
        // drop user
        DB::connection('mysql')->statement("DROP USER '$acc->db_username'@'localhost' ");
        $acc->delete();


        // delete MySQL User
    }


    public function status($id=0, $status='inactive'){
        $acc = Account::find($id);
        $acc->status = $status;
        $acc->save();
    }

    public function store(){
        // see if subdomain is available
        if(Account::where('subdomain', Input::get('subdomain'))->count() > 0){
            $return['status'] = 'danger';
            $return['text'] = 'This subdomain is not available.';
        }
        else{
            if(trim(Input::get('subdomain'))==''){
                $return['status'] = 'danger';
                $return['text'] = 'Cannot use blank subdomain';
            }
            else{
                // see if username is taken
                $new_name = '';
                if(strlen(Input::get('db_username'))>16 || Account::where('db_username', Input::get('db_username'))->count()>0){
                    do{
                        $new_name = substr(Input::get('db_name'),0, 13).rand(100,999);
                    }
                    while(Account::where('db_username', $new_name)->count() > 0);
                }
                $last = Account::orderBy('id', 'DESC')->limit(1)->first();
                $acc = new Account();
                $acc->subdomain = Input::get('subdomain');
                $acc->db_name = Input::get('db_name');
                $acc->db_username = $new_name!='' ? $new_name : Input::get('db_username');
                $acc->db_pass = Input::get('db_pass');
                $acc->status = 'active';
                $acc->installation = ($last->id+1).'-'.time();
                $acc->save();
                // create DB
                DB::connection('mysql')->statement("CREATE DATABASE $acc->db_name ;");
                $root_pass = Config::get('database.connections.mysql.password');
                $dump = "mysqldump -u root --password=$root_pass saas_template > template.dump ";
                $import = "mysql -u root --password=$root_pass $acc->db_name < template.dump";
                $delete = "rm template.dump";

                SSH::run(array(
                    $dump ,
                    $import,
                    $delete
                ));

                // create user
                DB::connection('mysql')->statement("CREATE USER '$acc->db_username'@'localhost' IDENTIFIED BY '$acc->db_pass' ;");
                // grant permission
                DB::connection('mysql')->statement("GRANT ALL ON $acc->db_name.* TO '$acc->db_username'@'localhost' ;");
                // populate settings db
                $email_from = $acc->subdomain.'@'.Config::get('app.base_url');
                DB::connection('mysql')->statement("UPDATE `$acc->db_name`.`settings`
                    SET `installation` = '$acc->installation',
                        `contact_email` = 'Your Email',
                        `contact_name` = '$acc->subdomain',
                        `email_from` = '$email_from' ,
                        `email_name` = '$acc->subdomain Admin',
                        `domain` = '$acc->subdomain'
                        ");
                DB::connection('mysql')->statement("UPDATE `$acc->db_name`.`users` SET `email` = '".Input::get('admin_email')."' ");

                $return['status'] = 'success';
                $return['text'] = View::make('saas._account')->withA($acc)->render();
            }
        }

        return json_encode($return);
    }
    
    public function admin($id){
        $dbdata = Account::find($id);
        Config::set('database.connections.mysql_tennant.database', $dbdata->db_name);
        Config::set('database.connections.mysql_tennant.username', $dbdata->db_username);
        Config::set('database.connections.mysql_tennant.password', $dbdata->db_pass);
        DB::setDefaultConnection('mysql_tennant');
        $admin = User::where('username','admin')->first();
        return View::make('saas.admin')->withAdmin($admin)->withAccount($id);
    }
    
    public function update_admin($id){
        $dbdata = Account::find($id);
        Config::set('database.connections.mysql_tennant.database', $dbdata->db_name);
        Config::set('database.connections.mysql_tennant.username', $dbdata->db_username);
        Config::set('database.connections.mysql_tennant.password', $dbdata->db_pass);
        DB::setDefaultConnection('mysql_tennant');
        $admin = User::find(Input::get('pk'));
        $field = Input::get('name');
        $admin->$field = Input::get('value');
        if(!$admin->updateUniques()){
            return format_validation_errors($admin->errors()->all());
        }
    }
    
    public function external_domains($id){
        $domains = DB::table('external_domains')->where('account_id', $id)->get();
        return View::make('saas.external_domains')->with( compact('domains') )->withAccount( $id );
    }
    
    public function set_external_domains($id){
        DB::table('external_domains')->where('id', Input::get('pk'))->update( ['domain' => trim( Input::get('value'), '/' ) ] );
    }
    
    public function destroy_external_domain($id){
         DB::table('external_domains')->where('id', $id)->delete();
    }
    
    public function create_external_domain($account){
        $domain = DB::table('external_domains')->insert([
            'account_id' => $account,
            'domain' => Input::get('domain')
        ]);
        $domain = DB::table('external_domains')->where( 'account_id' , $account)->where( 'domain', Input::get('domain') )->first();
        $response['status'] = 'success';
        $response['text'] = View::make('saas.external_domain')->with( compact('domain') )->render();
        return json_encode($response);
    }



}
