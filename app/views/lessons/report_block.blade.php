<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <div class="col-lg-8">
          <span id="block-title-span-{{$block->id}}">[#{{$block->id}}]
              {{($block->title=='') ? '[Title Not Set]' : $block->title}}</span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">
      
      <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Title" /><br />
      
      <div id='block-editor-{{$block->id}}' class='summernote_editor'>
      {{ ($block->text)}}
      </div><br />
       {{ Form::select('s', 
                  array('0' => 'Standalone Block', '1' => 'Included in section'), 
                  $block->in_section,
                  array(
                    'id' => "in_section-$block->id",
                    'class' => "form-inline"
                    )
                ) }}
      <button class='btn btn-danger save-btn' onclick='save_text_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
      <button type="button" class="btn btn-primary" onclick="add_report_tag({{$block->id}})">Add Tag</button>
      
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             


{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>