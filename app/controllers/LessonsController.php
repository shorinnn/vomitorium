<?php

class LessonsController extends BaseController {
        public $meta;
        private $program;
        
        /**
        * Instantiate a new UserController instance.
        */
       public function __construct()
       {
//           if(!Session::has('program_id') && get_programs()->count() > 0){
//               $p = get_programs();
//               Session::set('program_id', $p[0]->id);
//           }
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
            $meta['header_img_text'] = $meta['pageTitle'] = 'Course Editor';
            $lessons = Lesson::where('program_id',$this->program->id)->where('chapter_id',0)->orderBy('chapter_ord','asc')->orderBy('ord','asc')->paginate(15);
            if($lessons->count()==0) return  Redirect::to('modules');
            $data['chapters'] = editable_json( Chapter::where('program_id', Session::get('program_id'))->orderBy('ord','asc')->get(array('id','title')), 'title' , array('0' => 'None'));
            $data['lessons'] = $lessons;
            $meta['javascripts'] = array('../assets/js/admin/lessons.js');
            if(Request::ajax()){
                return View::make('lessons.lessons')->withData($data);
            }
            return View::make('lessons.index')->withData($data)->withMeta($meta);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            return View::make('lessons.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
            $chapter = Chapter::orderBy('ord','DESC')->where('program_id',Session::get('program_id'))->limit(1)->first();
            $lesson = new Lesson();
            $lesson->chapter_ord = 0;
            $lesson->chapter_id = 0;
            $lesson->program_id = $this->program->id;
            if($chapter!=null){
                $lesson->chapter_ord = $chapter->ord;
                $lesson->chapter_id = $chapter->id;
                $chapter = $chapter->id;
            }
            
            $lesson->title = Input::get('title');
            $lesson->ord =  DB::table('lessons')->where('chapter_id', $chapter)->max('ord') + 1;
            $lesson->slug = Str::slug($lesson->title);
            $lesson->meta_keywords = Input::get('meta_keywords','');
            $lesson->meta_description = Input::get('meta_description','');
            if($lesson->save()){
                $response['status'] = 'success';
                $response['text'] = "Lesson Created";
                $response['redirect_url'] = url("lessons/$lesson->id/editor");
            }
            else{
                $response['status'] = 'danger';
                $response['text'] = format_validation_errors($lesson->errors()->all());
            }
            return json_encode($response);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($lesson)
	{
            $lesson = Lesson::find(Input::get('pk'));
            return $lesson->update_field(Input::get('name'),Input::get('value'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($lesson)
	{
		$lesson = Lesson::find($lesson);
                $lesson->delete();
                return json_encode(array('status' => 'success', 'text' => 'Lesson Deleted'));
	}
        
        public function move($lesson, $direction){
            $lesson = Lesson::find($lesson);
            return  $lesson->move($direction);
        }
        
        public function move_block_to_pos(){
            $move = Input::get('move');
            $target = Input::get('target');
            return Lesson::move_block_to_pos($move, $target);
        }
        
        public function editor($lesson){
            
            $data['lesson'] = Lesson::where('program_id', Session::get('program_id'))->where('id',$lesson)->first();
            if($data['lesson'] == null) return Redirect::to('lessons');
            $data['blocks'] = Block::where('lesson_id', $lesson)->orderBy('ord','asc')->get();
            $data['chapters'] = editable_json( Chapter::get(array('id','title')), 'title' , array('0' => 'None'));
            $meta['javascripts'] = array('../assets/js/admin/lessons.js');
            $meta['header_img_text'] = $meta['page_title'] = 'Lesson Editor';
            $data['lessons'] = Lesson::where('program_id', Session::get('program_id'))->orderBy('chapter_ord','asc')->orderBy('ord','asc')->get();
            
            return View::make('lessons.editor')->withData($data)->withMeta($meta);
            
        }
        
        public function add_block($lesson){
            $block = new Block();
            $block->type = Input::get('type');
            if($block->type=='sortable_skills'){
                $block->type='sortable';
                $block->answer_type = 'Multiple Choice';
            }
            if($block->type=='question' ){
                $block->type = 'question';
                $block->answer_type = 'Open Ended';
                $block->scale_min_text = 'short';
            }
            if($block->type=='short' || $block->type=='one' || $block->type=='essay'){
                $block->answer_type = 'Open Ended';
                $block->scale_min_text = $block->type;
                $block->type = 'question';
            }
            
            if($block->type=='skill-select'){
                $block->type = 'Question';
                $block->answer_type = 'Skill Select';
            }
            if($block->type=='scale'){
                $block->type='question';
                $block->answer_type = 'Scale';
            }
            if($block->type=='mc'){
                $block->type='question';
                $block->answer_type = 'Multiple Choice';
            }
            if($block->type=='score'){
                $block->type='question';
                $block->answer_type = 'Score';
            }
            
            $block->lesson_id = $lesson;
            if(Input::get('pos')=='z'){
                DB::table('blocks')->where('lesson_id', $lesson)->increment('ord');
                $block->ord = 1;
            }
            else{
                DB::table('blocks')->where('lesson_id', $lesson)->where('ord','>=', Input::get('pos'))->increment('ord');
                $block->ord = Input::get('pos');
            }
            if(!$block->save()){
                $response['status'] = 'danger';
                $response['text'] = format_validation_errors($block->errors()->all());
            }
            else{
                $response['status'] = 'success';
                $lessons = Lesson::where('program_id', Session::get('program_id'))->orderBy('chapter_ord','asc')->orderBy('ord','asc')->get();
                if($block->type=='text') $response['html_string'] = View::make('lessons.text_block')->withBlock($block)->render();
                else if($block->type=='report') $response['html_string'] = View::make('lessons.report_block')->withBlock($block)->render();
                else if($block->type=='video') $response['html_string'] = View::make('lessons.video_block')->withBlock($block)->render();
                else if($block->type=='file') $response['html_string'] = View::make('lessons.file_block')->withBlock($block)->render();
                else if($block->type=='answer') $response['html_string'] = View::make('lessons.answer_block')->withBlock($block)->withLessons($lessons)->render();
                else if($block->type=='top_skills') $response['html_string'] = View::make('lessons.top_skills')->withBlock($block)->withLessons($lessons)->render();
                else if($block->type=='dynamic') $response['html_string'] = View::make('lessons.dynamic')->withBlock($block)->render();
                else if($block->type=='sortable') $response['html_string'] = View::make('lessons.sortable')->withBlock($block)->render();
                else if($block->type=='category') $response['html_string'] = View::make('lessons.category')->withBlock($block)->render();
                else if($block->type=='image_upload') $response['html_string'] = View::make('lessons.image_upload_block')->withBlock($block)->render();
                else if($block->type=='file_upload') $response['html_string'] = View::make('lessons.file_upload_block')->withBlock($block)->render();
                else{
                    if($block->answer_type=='Score') $response['html_string'] = View::make('lessons.score')->withBlock($block)->render();
                    else $response['html_string'] = View::make('lessons.question_block')->withBlock($block)->render();
                }
                $response['id'] = "block-$block->id";
            }
            
            return json_encode($response);
            
        }
        
        public function remove_block($block){
            $block = Block::find($block);
            @@unlink(base_path().'/assets/downloads/'.$block->text);
            if($block->delete()){
                $response['status'] = 'success';
                $response['text'] = 'Block deleted';
            }
            else{
                $response['status'] = 'danger';
                $response['text'] = format_validation_errors($block->errors()->all());
            }
            return json_encode($response);
        }
        
        public function move_block($block,$direction){
            $block = Block::find($block);
            return  $block->move($direction);
        }
        
        public function update_block($block){
            $block = Block::find($block);
            foreach(Input::all() as $field=>$value){
                $block->$field = $value;
            }
            if($block->updateUniques()){
                $response['status'] = 'success';
                $response['text'] = 'Saved';
            }
            else{
                $response['status'] = 'danger';
                $response['text'] = format_validation_errors($block->errors()->all());
            }
            return json_encode($response);
        }
        
        public function save_image(){
            $installation = sys_settings('installation');
            $name = Str::random();
            $file = Input::file('file');
            $filename = $installation.'-'.$name.'.'.$file->getClientOriginalExtension(); 
            $destination = base_path().'/assets/img/lessons/';
            $upload_success = Input::file('file')->move($destination, $filename);
            if( $upload_success ) {
                return url('assets/img/lessons/'.$filename);
            } else {
               return 'error';
            }
        }
        
        function save_file(){
            if(Input::get('no_file')==1) return;//no file update
            $file_max = ini_get('upload_max_filesize');
            
            $file_max_str_leng = strlen($file_max);
            $file_max_meassure_unit = substr($file_max,$file_max_str_leng - 1,1);
            $file_max_meassure_unit = $file_max_meassure_unit == 'K' ? 'kb' : ($file_max_meassure_unit == 'M' ? 'mb' : ($file_max_meassure_unit == 'G' ? 'gb' : 'units'));
            $file_max = substr($file_max,0,$file_max_str_leng - 1);
            $file_max_byte = intval(toByteSize($file_max.' '.$file_max_meassure_unit));
            
            
            
            try{
                $file = Input::file('file');
                $file_size = intval(toByteSize($file->getSize().' B'));
                if ($file_size==0 || $file_size > $file_max_byte) return "--error--Your file size exceedes our current limit ($file_max $file_max_meassure_unit).";
                $allowed = array('jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'pdf', 'odt', 'zip');
                $installation = sys_settings('installation');
                $name = Str::random();
                
                $filename = $installation.'-'.$name.'.'.$file->getClientOriginalExtension(); 
                if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)){
                    return '--error--This file type ('.$file->getClientOriginalExtension().') is not supported.';
                }

                $destination = base_path().'/assets/downloads/';
                $upload_success = Input::file('file')->move($destination, $filename);
                if( $upload_success ) {
                    // delete old file
                    $old = Block::find(Input::get('block'))->text;
                    @unlink(base_path().'/assets/downloads/'.$old);
                    return $filename;
                } else {
                   return '--error--An unexpected error was encountered.';
                }
                
            }
            catch(Exception $e){
                 $file = Input::file('file');
                 $file_size = intval(toByteSize($file->getSize().' B'));
                 if ($file_size > $file_max_byte) return "--error--Your file size exceedes our current limit ($file_max $file_max_meassure_unit)!";
            }
            
        }
        
        public function create_chapter($lesson){
            return View::make('lessons.create_chapter')->withChapters(Chapter::orderBy('ord','asc')->where('program_id',Session::get('program_id'))->get())->withLesson($lesson);
        }
        
        public function store_chapter(){
            $lesson = Lesson::find(Input::get('lesson'));
            return $lesson->create_chapter(Input::all());
        }
        
        public function all_chapters(){
            return editable_json( Chapter::orderBy('ord','asc')->where('program_id',Session::get('program_id'))->get(array('id','title')), 'title' , array('0' => 'None'));
        }
        
        public function view_lesson($id){
            $slug = Lesson::find($id)->slug;
            return Redirect::to('lesson/'.$slug);
        }        
        
        public function add_category(){
            $category = new Block_category();
            $category->category = Input::get('cat');
            $category->save();
            return $category->id;
        }
        
        public function get_answers($is_report = 0){
            if(!Session::has('program_id')) return 'Please select a program first';
            $lessons = Lesson::where('program_id', Session::get('program_id'))->orderBy('chapter_ord','asc')->orderBy('ord','asc')->get();
            return View::make('lessons.answers_tag')->withLessons($lessons)->with('is_report',$is_report)->render();
        }
      
}
