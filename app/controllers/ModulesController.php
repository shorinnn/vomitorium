<?php

class ModulesController extends BaseController {
        public $meta;
        private $program;
        
        /**
        * Instantiate a new UserController instance.
        */
       public function __construct()
       {
           $this->beforeFilter('admin');
           $this->beforeFilter('program', array('except'=>array('get_answers')));
           $this->program = Program::find(Session::get('program_id'));           
       }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            $meta['header_img_text'] = $meta['pageTitle'] = 'Modules';
          
            $data['chapters'] = Chapter::orderBy('ord','asc')->where('program_id', Session::get('program_id'))->get();
            $meta['javascripts'] = array('../assets/js/admin/modules.js');
            if(Request::ajax()){
                return View::make('modules.lessons')->withData($data);
            }
            return View::make('modules.index')->withData($data)->withMeta($meta);
	}

	public function store()
	{
            $chapter = new Chapter();
            $chapter->title = Input::get('module-name');
            $chapter->program_id = Session::get('program_id');
            $chapter->ord = 0;
            $chapter->published = 1;
            if(!$chapter->save()){
                $response['text'] = "Cannot create this module: <br />".format_validation_errors($chapter->errors()->all());
                $response['status'] = 'danger';
                return json_encode($response);
            }
            //DB::table('chapters')->where('program_id',Session::get('program_id'))->max('ord') + 1;
            if(Input::get('position')=='z'){
                $chapter->ord = DB::table('chapters')->where('program_id',Session::get('program_id'))->max('ord') + 1;
                //DB::table('chapters')->where('program_id',Session::get('program_id'))->max('ord') + 1;DB::table('chapters')->where('program_id', $chapter->program_id)->increment('ord');
            }
            else if(Input::get('position')=='y'){
                $chapter->ord = 1;
                DB::table('chapters')->where('program_id', $chapter->program_id)->increment('ord');
            }
            else{
                $other_chapter = Chapter::find(Input::get('position'));
                $chapter->ord = $other_chapter->ord + 1;
                DB::table('chapters')->where('program_id', $chapter->program_id)->where('ord', '>=', $chapter->ord )->increment('ord');
            }
            
            if( !$chapter->updateUniques() ){
                $response['text'] = "Cannot save this module: <br />".format_validation_errors($chapter->errors()->all());
                $response['status'] = 'danger';
                return json_encode($response);
            }
            
            for($i = 0; $i<Input::get('lesson-count'); ++$i){
                $l = new Lesson();
                $l->program_id = Session::get('program_id');
                $l->chapter_id = $chapter->id;
                $l->ord = DB::table('lessons')->where('chapter_id', $chapter->id)->where('program_id',Session::get('program_id'))->max('ord') + 1;
                $l->title = $chapter->title.' - Untitled Lesson '.($i+1);
                $l->slug = Str::slug($l->title);
                $l->chapter_ord = $chapter->ord;
                if(!$l->save()){
                    $response['text'] = "Module created but cannot create lesson: <br />".format_validation_errors($l->errors()->all());
                    $response['status'] = 'danger';
                    return json_encode($response);
                }
            }
            $response['text'] = 'Module created';
            $response['status'] = 'success';
            $response['id'] = $chapter->id;
            $data['chapters'] = array($chapter);
            $response['html'] = View::make('modules.modules')->withData($data)->render();
            return json_encode($response);
            
	}
        
        public function store_lesson(){
            $chapter = Chapter::find(Input::get('id'));
            $l = new Lesson();
            $l->program_id = Session::get('program_id');
            $l->chapter_id = Input::get('id');
            $l->ord = DB::table('lessons')->where('chapter_id', $l->chapter_id)->where('program_id',Session::get('program_id'))->max('ord') + 1;
            $l->title = $chapter->title. ' - Untitled Lesson '.date('H:i:s');
            $l->slug = Str::slug($l->title);
            $l->chapter_ord = $chapter->ord;
            $l->save();
            
            $lessons = array($l);
            $response['id'] = $l->id;
            $response['html'] = View::make('modules.lessons')->withLessons($lessons)->withC($chapter)->render();
            return json_encode($response);
        }
        
        public function move_lesson(){
            $move = intval(Input::get('move'));
            $target = intval(Input::get('target'));
            return Lesson::move_after($move, $target);
        }
        
        public function move_chapter(){
            $move = intval(Input::get('move'));
            $target = intval(Input::get('target'));
            return Chapter::move_after($move, $target);
        }
}