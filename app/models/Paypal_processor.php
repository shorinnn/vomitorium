<?php
use LaravelBook\Ardent\Ardent;

class Paypal_processor extends Ardent {
        public static function ipn(){
            define('USE_SANDBOX', true);
            define('DEBUG', 0);

            //https://github.com/paypal/ipn-code-samples/blob/master/paypal_ipn.php
            // Read POST data
            // reading posted data directly from $_POST causes serialization
            // issues with array data in POST. Reading raw POST data from input stream instead.
            $raw_post_data = file_get_contents('php://input');
            $raw_post_array = explode('&', $raw_post_data);
            $myPost = array();
            foreach ($raw_post_array as $keyval) {
                    $keyval = explode ('=', $keyval);
                    if (count($keyval) == 2)
                            $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
            // read the post from PayPal system and add 'cmd'
            $req = 'cmd=_notify-validate';
            if(function_exists('get_magic_quotes_gpc')) {
                    $get_magic_quotes_exists = true;
            }
            foreach ($myPost as $key => $value) {
                    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                            $value = urlencode(stripslashes($value));
                    } else {
                            $value = urlencode($value);
                    }
                    $req .= "&$key=$value";
            }

            // Post IPN data back to PayPal to validate the IPN data is genuine
            // Without this step anyone can fake IPN data

            if(USE_SANDBOX == true) {
                    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
            } else {
                    $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
            }

            $ch = curl_init($paypal_url);
            if ($ch == FALSE) {
                    return FALSE;
            }

            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

            // CONFIG: Optional proxy configuration
            //curl_setopt($ch, CURLOPT_PROXY, $proxy);
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

            // Set TCP timeout to 30 seconds
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

            // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
            // of the certificate as shown below. Ensure the file is readable by the webserver.
            // This is mandatory for some environments.

            //$cert = __DIR__ . "./cacert.pem";
            //curl_setopt($ch, CURLOPT_CAINFO, $cert);

            $res = curl_exec($ch);
            if (curl_errno($ch) != 0) // cURL error
                {
                if(DEBUG == true) {	
                        error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
                }
                curl_close($ch);
                exit;

            } else {
                // Log the entire HTTP response if debug is switched on.
                curl_close($ch);
            }

            // Inspect IPN validation result and act accordingly

            if (strcmp ($res, "VERIFIED") == 0) {
                $custom = json_decode(urldecode($_POST['custom']), true);
                $plan = PaymentPlan::find($custom['p']);
                $processor = PaymentProcessor::where('name','Paypal')->where('program_id',$plan->program_id)->first();
                if($processor==null){
                    mail('shorinnn@yahoo.com','processor','processor');
                    return;
                }
                // check whether the payment_status is Completed
                if(strtolower($_POST['payment_status']) != 'completed') {
                    mail('shorinnn@yahoo.com','status','status');
                    return;
                }
                // check that txn_id has not been previously processed
                if(Paypal_transaction::where('transaction_id', $_POST['txn_id'])->count() > 0) {
                    mail('shorinnn@yahoo.com','txn','txn');
                    return;
                }
                // check that receiver_email is your PayPal email
                 if(strtolower($_POST['receiver_email']) != strtolower($processor->field)) {
                    mail('shorinnn@yahoo.com','receiver_email','receiver_email');
                    return;
                }
                // check that payment_amount/payment_currency are correct
                if($_POST['mc_currency']!='USD') {
                    mail('shorinnn@yahoo.com','currency','currency');
                    return;
                }
                if($_POST['payment_gross']!=$plan->cost) {
                    mail('shorinnn@yahoo.com','cost','cost');
                    return;
                }
                
                // process payment and mark item as paid.
                
                $t = new Paypal_transaction();
                $t->user_id = $custom['u'];
                $t->transaction_id = $_POST['txn_id'];
                $t->payment_plan_id = $custom['p'];
                $t->save();
                try{
                    
                    if(DB::table('programs_users')->where('user_id', $custom['u'])->where('subscription_id', $plan->id)->count()==0){
                        $data['program_id'] = $plan->program_id;
                        $data['user_id'] = $custom['u'];
                        $data['subscription_id'] = $plan->id;
                        $data['start_date'] = date('Y-m-d H:i:s');
                        DB::table('programs_users')->insert($data);
                    }
                    else{
                         mail('shorinnn@yahoo.com','no save', 'no program save');
                    }
                }
                catch(Exception $e){
                    mail('shorinnn@yahoo.com','Exception', $e->getMessage());
                }

            } else if (strcmp ($res, "INVALID") == 0) {
                // log for manual investigation
                // Add business logic here which deals with invalid IPN messages
            }
            
            
        }
}
