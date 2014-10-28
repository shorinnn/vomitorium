<?php
use LaravelBook\Ardent\Ardent;

class Stripe_processor extends Ardent {
        public static function purchase($meta){
            require base_path().'/vendor/Stripe/Stripe.php';
            $plan = PaymentPlan::find($_POST['plan_id']);
            Stripe::setApiKey(PaymentProcessor::where('program_id', $plan->program_id)->where('name','Stripe')->first()->field2);
            // Get the credit card details submitted by the form
            $token = $_POST['stripeToken'];
            
            // Create a Customer
            if(Auth::user()->stripe_customer_id ==''){
                try{
                    $customer = Stripe_Customer::create(array(
                      "card" => $token,
                      "description" => $_POST['stripeEmail'])
                    );
                }
                catch(Exception $e) {
                  // The card has been declined
                    Return View::make('payment_plans.stripe_card_declined')->withException($e)->withMeta($meta);
                }
                Auth::user()->stripe_customer_id = $customer->id;
                Auth::user()->updateUniques();
            }
            // use existing customer
            else{
                // see if this card has been used before
                $customer = Stripe_Customer::retrieve(Auth::user()->stripe_customer_id);
                $card = $customer->cards->create(array("card" => $token));
                $db_card = Stripe_transaction::where('user_id', Auth::user()->id)->where('card_fingerprint', $card->fingerprint)->get();
                // it's new, add it to the customer
                if($db_card->count()==0){
                    $customer->default_card = $card ;
                    $customer->save();
                }
                // used before, delete this duplicate and use the existing one
                else{
                    $customer->cards->retrieve($card->id)->delete();
                    $card = $customer->cards->retrieve($db_card->first()->card_id);
                    $customer->default_card = $card ;
                    $customer->save();
                }
                 
            }
            $plan = PaymentPlan::find($_POST['plan_id']);
            if($plan->type=='subscription' && !Session::has('trial')) return self::subscribe($customer, $plan, $meta);
            else return self::charge($customer, $plan, $meta);
            
            
        }
        
        public static function charge($customer, $plan, $meta){
            // Charge the Customer instead of the card
            try{
                $charge = Stripe_Charge::create(array(
                  "amount" => $plan->cost*100, # amount in cents, again
                  "currency" => "usd",
                  "customer" => $customer->id
                    )
                );
                // save the transaction details
                $t = new Stripe_transaction();
                $t->user_id = Auth::user()->id;
                $t->transaction_id = $charge->id;
                $t->card_id = $charge->card->id;
                $t->card_fingerprint = $charge->card->fingerprint;
                $t->payment_plan_id = $plan->id;
                $t->save();
                Auth::user()->updateUniques();
                if(DB::table('programs_users')->where('user_id', Auth::user()->id)->where('subscription_id', $plan->id)->count()==0){
                    $data['program_id'] = $plan->program_id;
                    $data['user_id'] = Auth::user()->id;
                    $data['subscription_id'] = $plan->id;
                    $data['start_date'] = date('Y-m-d H:i:s');
                    if(Session::has('trial')){
                        $unit = singplural(1, $plan->trial_duration_unit);
                        $data['expires'] = date('Y-m-d 23:59:59', strtotime("+ $plan->trial_duration $unit"));
                    }
                    DB::table('programs_users')->insert($data);
                }
                Session::set('program_id', $plan->program_id);
                Session::forget('payment_plan_id');
                Session::forget('trial');
                Return View::make('payment_plans.stripe_success')->withMeta($meta);
            }
            catch(Exception $e) {
                // The card has been declined
                Return View::make('payment_plans.stripe_card_declined')->withException($e)->withMeta($meta);
            }
        }
        
        public static function subscribe($customer, $plan, $meta){
            $subscribed_before = DB::table('programs_users')->where('user_id', Auth::user()->id)->where('subscription_id', $plan->id)->count();
            $trial = 0;
            // see if plan was created on stripe
            try{
                $stripe_plan = Stripe_Plan::retrieve($plan->id);
            }
            catch(Exception $e){// create stripe plan
                $stripe_plan = array(
                    "amount" => $plan->cost * 100, 
                    "interval" => singplural(1, $plan->subscription_duration_unit),
                    "interval_count" => $plan->subscription_duration,
                    "name" => $plan->name,
                    "currency" => "usd",
                    "id" => $plan->id);
                
                if($plan->type=='subscription' && $plan->trial_duration>0){
                    if($plan->trial_duration_unit=='days') $trial = $plan->trial_duration;
                    else $trial = $plan->trial_duration * 30;
                    $stripe_plan['trial_period_days'] = $trial;
                }
                $stripe_plan = Stripe_Plan::create($stripe_plan);
            }
            // calculate the number of trial days
            if($plan->trial_duration_unit=='days') $trial = $plan->trial_duration;
            else $trial = $plan->trial_duration * 30;
            
            try{
                $plan_data = array("plan" => $plan->id);
                // if subscribed before, no free trial
                if($subscribed_before > 0) $plan_data['trial_end'] = 'now';
                $subscription = $customer->subscriptions->create($plan_data);
                
                if($plan->trial_cost>0 && $subscribed_before==0){
                    try{
                         $charge = Stripe_Charge::create(array(
                          "amount" => $plan->trial_cost*100, # amount in cents, again
                          "currency" => "usd",
                          "customer" => $customer->id
                            )
                        );
                        // save the transaction details
                        $t = new Stripe_transaction();
                        $t->user_id = Auth::user()->id;
                        $t->transaction_id = $charge->id;
                        $t->card_id = $charge->card->id;
                        $t->card_fingerprint = $charge->card->fingerprint;
                        $t->payment_plan_id = $plan->id;
                        $t->save();
                    }
                    catch(Exception $e){
                        Return View::make('payment_plans.stripe_card_declined')->withException($e)->withMeta($meta);
                    }
                }
                if(DB::table('programs_users')->where('user_id',Auth::user()->id)->where('program_id', $plan->program_id)
                        ->where('subscription_id', $plan->id)->count()==0){
                    $data['program_id'] = $plan->program_id;
                    $data['subscription_id'] = $plan->id;
                    $data['stripe_subscription_id'] = $subscription->id;
                    $data['user_id'] = Auth::user()->id;
                    $data['start_date'] = date('Y-m-d H:i:s');
                    $unit = singplural(1, $plan->subscription_duration_unit);
                    //$data['expires'] = date('Y-m-d 23:59:59');//date('Y-m-d 23:59:59', strtotime("+ $plan->subscription_duration $unit"));
                    $data['expires'] = date('Y-m-d 23:59:59', strtotime("+ $trial day"));
                    DB::table('programs_users')->insert($data);
                }
                else{
                    DB::table('programs_users')->where('user_id',Auth::user()->id)->where('program_id', $plan->program_id)
                        ->where('subscription_id', $plan->id)->update(array('stripe_subscription_id'=>$subscription->id));
                }
                
                Session::set('program_id', $plan->program_id);
                Session::forget('payment_plan_id');
                Session::forget('trial');
                Return View::make('payment_plans.stripe_success')->withMeta($meta);
            }
            catch(Exception $e){
                 // The card has been declined
                Return View::make('payment_plans.stripe_card_declined')->withException($e)->withMeta($meta);
            }
        }
        
        public static function cancel_subscription($id){
            require base_path().'/vendor/Stripe/Stripe.php';
            $program_id = DB::table('programs_users')->where('stripe_subscription_id', $id)->first()->program_id;
            Stripe::setApiKey(PaymentProcessor::where('program_id',$program_id)->where('name','Stripe')->first()->field2);
            try{
                $cu = Stripe_Customer::retrieve(Auth::user()->stripe_customer_id);
                $cu->subscriptions->retrieve($id)->cancel();
                $return['text'] = 'Subscription cancelled';
                $return['status'] = 'success';
                return json_encode($return);
            }
            catch(Exception $e){
                $return['text'] = 'An error occurred - could not cancel subscription'.$e->getMessage();
                $return['status'] = 'danger';
                return json_encode($return);
            }
        }

        
        public static function process_hook(){
            require base_path().'/vendor/Stripe/Stripe.php';
            $input = @file_get_contents("php://input");
            $event_json = json_decode($input);
            
            
            if($event_json->type=='invoice.payment_succeeded' && $event_json->data->object->charge!=null){
                $plan_id = $event_json->data->object->lines->data[0]->plan->id;
                $plan = PaymentPlan::find($plan_id);
                Stripe::setApiKey(PaymentProcessor::where('program_id', $plan->program_id)->where('name','Stripe')->first()->field2);
                try{
                    $customer = $event_json->data->object->customer;
                    $user = User::where('stripe_customer_id', $customer)->first();
                    $unit = singplural(1, $plan->subscription_duration_unit);
                    //$expiry = DB::table('programs_users')->where('user_id', $user->id)->where('subscription_id', $plan->id)->first();
                    $expires = date('Y-m-d H:i:s');
                    $data['expires'] = date('Y-m-d 23:59:59', strtotime(" $expires + $plan->subscription_duration $unit"));
                    $data['subscription_cancelled'] = null;
                    DB::table('programs_users')->where('user_id', $user->id)->where('subscription_id', $plan->id)->update($data);
                    // save the transaction details
                    $charge = Stripe_Charge::retrieve($event_json->data->object->charge);
                    $t = new Stripe_transaction();
                    $t->user_id = $user->id;
                    $t->transaction_id = $charge->id;
                    $t->card_id = $charge->card->id;
                    $t->card_fingerprint = $charge->card->fingerprint;
                    $t->payment_plan_id = $plan_id;
                    $t->save();
                }
                catch(Exception $e){
                    echo $e->getMessage();
                }
              
            }
            if($event_json->type=='customer.subscription.deleted'){
                $plan_id = $event_json->data->object->plan->id;
                $plan = PaymentPlan::find($plan_id);
                Stripe::setApiKey(PaymentProcessor::where('program_id', $plan->program_id)->where('name','Stripe')->first()->field2);
            
                $customer = $event_json->data->object->customer;
                $user = User::where('stripe_customer_id', $customer)->first();
                $data['subscription_cancelled'] = date('Y-m-d H:i:s');
                DB::table('programs_users')->where('user_id', $user->id)->where('subscription_id', $plan_id)->update($data);
            }
        }
}
