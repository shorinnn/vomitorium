<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <div class="col-lg-8">
          <span id="xblock-title-span-{{$block->id}}">[#{{$block->id}}]
            <!-- {{($block->title=='') ? '[Title Not Set]' : $block->title}}-->
          Text/HTML</span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">
      
      <div id='block-editor-{{$block->id}}' class='summernote_editor'>{{$block->text}}</div>
      <br />
      
      <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" 
             placeholder="Headline (optional)" /><br />
      <!--<textarea id='block-editor-{{$block->id}}' class='summernote_editor'>{{$block->text}}</textarea><br />-->
    
      <button class='btn btn-danger save-btn' onclick='save_text_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
      <button type="button" class="btn btn-primary" onclick="add_tag({{$block->id}})">Add Tag</button>
      <button type="button" class="btn btn-link" onclick="tag_options()">Tag Options</button>
      
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>   
       
{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>
