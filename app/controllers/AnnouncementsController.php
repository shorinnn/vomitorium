<?php

class AnnouncementsController extends BaseController {

    public function __construct(){
        $this->beforeFilter('admin');
        $this->beforeFilter('program');
    }
    
    public function index(){
        $p = Program::find(Session::get('program_id'));
        $announcements = $p->announcements;
        $meta['header_img_text'] = 'Anouncements';
        return View::make('announcements.index')->withAnnouncements($announcements)->withMeta($meta);
    }
    
    public function update(){
        $a = Announcement::find(Input::get('pk'));
        $field = Input::get('name');
        if($field=='start_date' || $field=='end_date'){
           $a->$field = date('Y-m-d', strtotime(Input::get('value')));
        }
        else $a->$field = Input::get('value');
        if(!$a->updateUniques()){
            return Response::make(format_validation_errors($a->errors()->all()), 400);
        }
    }
    public function store(){
        $a = new Announcement();
        $a->content = Input::get('content');
        $a->start_date = date('Y-m-d', strtotime(Input::get('start_date')));
        $a->end_date = date('Y-m-d', strtotime(Input::get('end_date')));
        $a->program_id = Session::get('program_id');
        $a->user_id = Auth::user()->id;
        $a->published = 1;
        if(!$a->save()){
            $response['status'] = 'danger';
            $response['text'] = format_validation_errors($a->errors()->all());
        }
        else{
            $response['status'] = 'success';
            $response['text'] = 'Announcement Created';
            $response['html'] = View::make('announcements.announcement')->withAnnouncement($a)->render();
            $response['identifier'] = ".list-row-$a->id";
        }
        return json_encode($response);
    }

    function destroy($id){
        $a = Announcement::find($id);
        $a->delete();
        return json_encode(array('status' => 'success', 'text' => 'Announcement Deleted'));
    }

}
