<?php
class Mailer extends Eloquent {
    
    public static function contact($data){
        $return = Mail::send('emails.contact', $data, function($message){
            $message->to(sys_settings('contact_email'), sys_settings('contact_name'))->subject('New Contact Message');
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
}