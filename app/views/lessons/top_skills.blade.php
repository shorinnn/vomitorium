<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <span>[#{{$block->id}}]
          Top Skills</span>
      {{View::make('lessons.block_buttons')->withBlock($block)}}
  </div>
  <div class="panel-body">
      <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" placeholder="Subtitle" /><br />
      
         Skill type:<br />
      {{ Form::select('s', 
                  array('Functional' => 'Functional', 'Personality' => 'Personality'), 
                  $block->top_skill_type,
                  array(
                    'id' => "block-top-skill-type-$block->id",
                    'class' => "form-control"
                    )
                ) }}
    <br />
        Number of skills to list: 
        <input type='text' class='form-control' id='block-top-skill-count-{{$block->id}}' value='{{$block->top_skill_count}}' />
        <br />
       {{ Form::select('s', 
                  array('0' => 'Standalone Block', '1' => 'Included in section'), 
                  $block->in_section,
                  array(
                    'id' => "in_section-$block->id",
                    'class' => "form-inline"
                    )
                ) }}

      <button class='btn btn-danger save-btn' onclick='save_top_skill_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>