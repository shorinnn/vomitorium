<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <span id="block-title-span-{{$block->id}}">[#{{$block->id}}]
              {{($block->title=='') ? '[Title Not Set]' : $block->title}}</span>
      {{View::make('lessons.block_buttons')->withBlock($block)}}
  </div>
  <div class="panel-body">
      <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Title" /><br />
        <?php $cat = DB::table('block_categories')->get();
        $cats = array();
        foreach($cat as $c){
            $cats[$c->id] = $c->category;
        }
        ?>
        Category: {{ Form::select('s', 
                  $cats, 
                  $block->category_id,
                  array(
                    'id' => "block-cat-$block->id",
                    'class' => "form-inline"
                    )
                ) }}
      <br />
      <br />
       {{ Form::select('s', 
                  array('0' => 'Standalone Block', '1' => 'Included in section'), 
                  $block->in_section,
                  array(
                    'id' => "in_section-$block->id",
                    'class' => "form-inline"
                    )
                ) }}

      <button class='btn btn-danger save-btn' onclick='save_category_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
  </div>             


{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>
