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
}