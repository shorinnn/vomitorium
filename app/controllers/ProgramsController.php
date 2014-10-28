<?php

class ProgramsController extends BaseController {

    public function __construct(){
        $this->beforeFilter('admin', array('except'=>'choose'));
    }
    
    public function index(){
        $programs = Program::all();
        $meta['header_img_text'] = 'Programs';
        return View::make('programs.index')->withPrograms($programs)->withMeta($meta);
    }
    
    public function update(){
        $p = Program::find(Input::get('pk'));
        $field = Input::get('name');
        $p->$field = Input::get('value');
        if(!$p->updateUniques()){
            return Response::make(format_validation_errors($p->errors()->all()), 400);
        }
    }
    public function store(){
        $p = new Program();
        $p->name = Input::get('program');
        
        if(Input::has('just_program')){
            if(!$p->save()){
                $response['status'] = 'danger';
                $response['text'] = format_validation_errors($p->errors()->all());
            }
            else{
                $response['status'] = 'success';
                $response['text'] = 'Program Created';
                $response['html'] = View::make('programs.program')->withProgram($p)->render();
                $response['identifier'] = ".list-row-$p->id";
                if(Input::has('from_dash')) Session::set('program_id', $p->id);
            }
            return json_encode($response);
        }
        $p->save();
        Session::set('program_id', $p->id);
        return View::make('programs.options')->render();
    }
    
    function choose($id){
        Session::set('program_id', $id);
        if($id==0) Session::forget('program_id');
        else{
            $state = json_decode(Auth::user()->state);
            $state->last_program = $id;
            Auth::user()->state = json_encode($state);
            Auth::user()->updateUniques();
        }
        return $id;
    }
    
    function destroy($id){
        $program = Program::find($id);
        $program->delete();
        if($id==Session::get('program_id')) Session::forget('program_id');
        return json_encode(array('status' => 'success', 'text' => 'Program Deleted'));
    }

}
