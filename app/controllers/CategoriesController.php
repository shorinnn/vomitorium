<?php

class CategoriesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            $cats = Block_category::paginate(15);
            if(Request::ajax()){
                return View::make('categories.categories')->withCats($cats);
            }
            $meta['header_img_text'] = $meta['pageTitle'] = 'Block Category Manager';
            return View::make('categories.index')->withCats($cats)->withMeta($meta);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('categories.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('categories.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('categories.edit');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
            $cat = Block_category::find(Input::get('pk'));
            $field = Input::get('name');
            $cat->$field = Input::get('value');
            $cat->save();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
            Block_category::find($id)->delete();
            return json_encode(array('status' => 'success', 'text' => 'Category Deleted'));
	}

}
