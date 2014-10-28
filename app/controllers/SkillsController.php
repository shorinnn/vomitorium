<?php

class SkillsController extends BaseController {
        public function __construct() {
            $this->beforeFilter('admin');
        }

	public function index()
	{
            $skills = Skill::orderBy('id','asc')->get();
            $meta['header_img_text'] = 'Skill-block Options';
            return View::make('skills.index')->withSkills($skills)->withMeta($meta);
	}

	
	public function update()
	{
		$skill = Skill::find(Input::get('pk'));
                $skill->values = Input::get('value');
                if($skill->save()){
                    return Response::make('success', 200);
                }
                else return Response::make(format_validation_errors($skill->errors()->all()), 400); 
	}

}
