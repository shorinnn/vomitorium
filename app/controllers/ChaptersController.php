<?php

class ChaptersController extends \BaseController {

    public $meta;
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->beforeFilter('admin');
        $this->beforeFilter('program');
    }
    
    public function index(){
        $data['last_ord'] = DB::table('chapters')->max('ord');
        $data['chapters'] = Chapter::where('program_id', Session::get('program_id'))->orderBy('ord','asc')->paginate(15);
        
        if(Request::ajax()){
            return View::make('chapters.chapters')->withData($data);
        }
        $this->meta['header_img_text'] = $this->meta['pageTitle'] = 'Chapter Editor';
        $this->meta['javascripts'] = array('../assets/js/admin/chapters.js');
        return View::make('chapters.index')->withMeta($this->meta)->withData($data);
    }
    
    public function create(){
       
        return View::make('chapters.create');
    }
    
    public function store(){
        $chapter = new Chapter();
        $chapter->title = Input::get('title');
        $chapter->ord = DB::table('chapters')->where('program_id',Session::get('program_id'))->max('ord') + 1;
        $chapter->program_id = Session::get('program_id');
        if($chapter->save()){
            $response['status'] = 'success';
            $response['text'] = "Chapter Created";
        }
        else{
            $response['status'] = 'danger';
            $response['text'] = format_validation_errors($chapter->errors()->all());
        }
        return json_encode($response);
    }
    
    public function update(){
        $chapter = Chapter::find(Input::get('pk'));
        $field = Input::get('name');
        $chapter->$field = Input::get('value');
         if($chapter->updateUniques()){
            return Response::make('success', 200);
        }
        else{
            return Response::make(format_validation_errors($chapter->errors()->all()), 400);
        }
    }
    
    public function destroy($id){
        $chapter = Chapter::find($id);
        $chapter->delete();
        return json_encode(array('status' => 'success', 'text' => 'Chapter Deleted'));
    }
    
    public function move($direction, $id){
        $chapter= Chapter::find($id);
        return  $chapter->move($direction);
    }

}
