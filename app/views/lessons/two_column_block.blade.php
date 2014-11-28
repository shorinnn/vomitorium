<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <span id="block-title-span-{{$block->id}}">[#{{$block->id}}] Two Column</span>
      {{View::make('lessons.block_buttons')->withBlock($block)}}
  </div>
  <div class="panel-body">
       <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Title (e.g. Enter your data)" /><br />
       <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" placeholder="Instructions (e.g. Enter KPI and Target values)" /><br />
       Left Column Title <input type="text" class="form-control" id="scale-min-text{{$block->id}}" value="{{$block->scale_min_text}}" placeholder="Left Column Title (e.g. KPI)" /><br />
       Right Column Title <input type="text" class="form-control" id="scale-max-text{{$block->id}}" value="{{$block->scale_max_text}}" placeholder="Right Column Title (e.g. Target)" /><br />
       Number of Rows <input type="text" class="form-control" id="scale-min-{{$block->id}}" value="{{$block->scale_min}}" placeholder="Number of fillable rows (e.g. 2)" /><br />

      <button class='btn btn-danger save-btn' onclick='save_two_column_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
       
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>