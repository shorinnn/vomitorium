<?php

class LessonAlertsController extends BaseController {

    public function __construct(){
        $this->beforeFilter('admin');
    }
    
    public function show($id){
        $a = Lesson_alert::find($id);
        return View::make('lessons.alerts.content')->withAlert($a);
    }
    
    public function store(){
        $data = json_decode(Input::get('data'));
        $a = new Lesson_alert();
        $a->lesson_id = $data->lesson_id;
        $a->type = 'deadline';
        $a->delivery_type = 'email';
        
        if($a->save()){
            $response['status'] = 'success';
            $response['text'] = 'Notification Created';
            $response['html'] = View::make('lessons.alerts.alert')->withAlert($a)->render();
        }
        else{
            $response['status'] = 'danger';
            $response['text'] = 'Something bad happened...';
            $response['error'] = format_validation_errors($a->errors()->all());
        }
        
        return json_encode($response);
    }
    
    public function update($id){
        $alert = Lesson_alert::find($id);
        $field = Input::get('name');
        $alert->$field = Input::get('value');
        $alert->save();
    }
    
    public function destroy($id){
        $a = Lesson_alert::find($id);
        $a->delete();
        $response['status'] = 'success';
        $response['text'] = '';
        return json_encode($response);
    }
       

}
