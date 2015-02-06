<?php
class Mailer extends Eloquent {
    
    public static function contact($data){
        $return = Mail::send('emails.contact', $data, function($message) use ($data) {
            $message->to(sys_settings('contact_email'), sys_settings('contact_name'))->subject( $data['contact_subject'] );
        });
        if($return==1) {
            $response['status'] = 'success';
            $response['text'] = 'Thank you for your message!';
        }
        else{
            $response['status'] = 'danger';
            $response['text'] = 'An error occurred while trying to deliver your message.';
        }
        return json_encode($response);
    }
    
    public static function reply_notification($data){
        $user = User::find($data['user_id']);
        $admin = User::find($data['admin_id']);
        $data['text'] = nl2br(sys_settings('new_comment_email'));
        
        $data['text'] = str_replace('[FirstName]', $user->first_name, $data['text']);
        $data['text'] = str_replace('[LastName]', $user->last_name, $data['text']);
        $data['text'] = str_replace('[CoachFirstName]', $admin->first_name, $data['text']);
        $data['text'] = str_replace('[CoachLastName]', $admin->last_name, $data['text']);
        $data['text'] = str_replace('[Link]', "<a href='$data[link]'>$data[link]</a>", $data['text']);
        $return = Mail::send('emails.new_comment', $data, function($message) use ($data){
            $user = User::find($data['user_id']);
            $message->to($user->email, "$user->first_name $user->last_name")->subject('New Comment');
        });
    }
    
    public static function program_purchased($data){
        $text = "Hello $data[name] <br />Congratulations on purchasing ".$data['program']->name.".<br />Please sign in by going to ". action('UserController@login') ;
        if( trim( sys_settings('purchase_email_content') ) != ''){
            $text = str_replace('[CustomerName]', $data['name'], nl2br( sys_settings('purchase_email_content') ) );
            $text = str_replace('[ProgramName]', $data['program']->name, $text );
            $text = str_replace('[LoginLink]', action('UserController@login'), $text );
        }
        $data['text'] = $text;
        $return = Mail::send('emails.program_purchased', $data, function($message) use ($data){
            $message->to($data['email'], $data['name'])->subject( sys_settings('program_purchase_email_subject') );
        });
    }
    
    
    public static function free_access_registration($data){
        $text = "Hello $data[name] <br />Congratulations on registering for ".$data['program']->name.".<br />Please sign in by going to ". action('UserController@login') ;
        if( trim( sys_settings('purchase_email_content') ) != ''){
            $text = str_replace('[CustomerName]', $data['name'], nl2br( sys_settings('free_register_email_content') ) );
            $text = str_replace('[ProgramName]', $data['program']->name, $text );
            $text = str_replace('[LoginLink]', action('UserController@login'), $text );
        }
        $data['text'] = $text;
        $return = Mail::send('emails.program_purchased', $data, function($message) use ($data){
            $message->to($data['email'], $data['name'])->subject( sys_settings('free_register_email_subject') );
        });
    }
    
}