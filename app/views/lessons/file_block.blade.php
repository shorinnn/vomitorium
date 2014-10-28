<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
    
  <div class="panel-heading">
      <div class="col-lg-8">
          <span id="xblock-title-span-{{$block->id}}">[#{{$block->id}}]
              <!--{{($block->title=='') ? 'File' : $block->title}}-->File Download</span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">
      @if($block->title=='' && $block->text=='')
      <div class='first text-center'>
        <input type="file" style='display:inline' id='first-block-editor-{{$block->id}}'/> 
        <button class='btn btn-danger save-btn' onclick='save_first_file_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Upload File</button>
      </div>
      <div class='advanced' style='display: none'>
        Name This File For Your Name To See:
            <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="e.g. Your Guidebook" /><br />
            Description (Optional):
            <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" 
                   placeholder="e.g. Download this to make your coaching experience even better." /><br />

            <input type="file" id='block-editor-{{$block->id}}' class='col-lg-12' />
            <p class='file_link'>Download: {{link_to_asset('assets/downloads/'.$block->text, $block->title, array('target'=>'_blank'))}}</p>
              <br />
            
            <button class='btn btn-danger save-btn' onclick='save_file_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>

            {{View::make('lessons.categories')->withBlock($block)}}
      </div>
      @else
             Name This File For Your Name To See:
            <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="e.g. Your Guidebook" /><br />
            Description (Optional):
            <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" 
                   placeholder="e.g. Download this to make your coaching experience even better." /><br />

            <input type="file" id='block-editor-{{$block->id}}' class='col-lg-12' />
            @if($block->text!='')
            <p class='file_link'>Download: {{link_to_asset('assets/downloads/'.$block->text, $block->title, array('target'=>'_blank'))}}</p>
            @endif
              <br />
            
            <button class='btn btn-danger save-btn' onclick='save_file_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>

            {{View::make('lessons.categories')->withBlock($block)}}
      @endif
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>