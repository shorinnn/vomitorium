<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
    
  <div class="panel-heading">
      <div class="col-lg-8">
          <span id="block-title-zspan-{{$block->id}}">[#{{$block->id}}]
              File Upload </span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">
     Instructions:
    <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" 
           placeholder="e.g. Please submit screenshots of your campaigns" /><br />
    <button class='btn btn-danger save-btn' onclick='save_text_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>

            {{View::make('lessons.categories')->withBlock($block)}}
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>