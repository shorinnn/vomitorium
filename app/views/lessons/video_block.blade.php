<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <div class="col-lg-8">
          <span id="xblock-title-span-{{$block->id}}">[#{{$block->id}}]
             <!-- {{($block->title=='') ? 'Video' : $block->title}}-->Video</span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">
      
      <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Title" /><br />
      
      <textarea id='block-editor-{{$block->id}}' class='col-lg-12' placeholder="Enter Video Embed Code">{{($block->text)}}</textarea><br />
      <br />
      <br />
      <button class='btn btn-danger save-btn' onclick='save_text_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
      
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>