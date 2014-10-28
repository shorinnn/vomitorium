<?php
    $lists = array();
    $skills= DB::table('skills')->get();
    foreach($skills as $s){
        $lists[$s->id] = $s->type;
    }
?>
<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <span id="block-title-span-{{$block->id}}">[#{{$block->id}}] Dynamic Score</span>
      {{View::make('lessons.block_buttons')->withBlock($block)}}
  </div>
  <div class="panel-body">
       <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Title (e.g. How does this opportunity align with:)" /><br />
       <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" placeholder="Instructions (e.g. Score of 1 (low) to 10 (high)" /><br />
       Minimum Score <input type="text" class="form-control" id="scale-min-{{$block->id}}" value="{{$block->scale_min}}" placeholder="Minimum score value (e.g. 1)" /><br />
       Maximum Score <input type="text" class="form-control" id="scale-max-{{$block->id}}" value="{{$block->scale_max}}" placeholder="Maximum score value (e.g. 10)" /><br />

       <div class='scale-options-holder' id='scale-options-holder-{{$block->id}}'>
            @if($block->choices !='')
                <?php
                    $j = 0;
                    $choices = json_decode($block->choices, true); ?>
                @foreach($choices as $choice)
                <?php 
                    ++$j;
                    $id = 'scale-option-' . time() . rand(100, 999); ?>
                <div class="option" id="{{$id}}">
                  <div class="option-number">{{$j}}</div>
                  <div class="option-value">
                      <input type='text' placeholder="Option" class="option-input" value="{{br2nl($choice)}}" />
                  </div>
                  <div class="option-cancel">
                      <span onclick="remove_option('{{$id}}')">
                          <i class="mainsprite sprite_close2"></i></span>
                  </div>
              </div>
                @endforeach
            @endif
        </div>
        <div class="col-md-12">
            <div class="center">
              <button class="normalbutton" onclick='add_scale_option({{$block->id}})'><span>Add Entry</span></button>
            </div>
          </div>
       
      <button class='btn btn-danger save-btn' onclick='save_score_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
       
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>