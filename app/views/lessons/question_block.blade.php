<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <div class="col-lg-8">
          <span id="block-title-span-{{$block->id}}">[#{{$block->id}}]
              <!--{{($block->title=='') ? '[Title Not Set]' : $block->title}}-->
          @if($block->answer_type=='Open Ended')
            Open Ended Question
          @elseif($block->answer_type=='Scale')
            Scale
          @elseif($block->answer_type=='Multiple Choice')
            Multiple Choice
          @endif
          </span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">
      
      <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Enter Question..." /><br />
      <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" placeholder="(Optional) Additional instructions for user. e.g. Write down at least 3 specific points" /><br />
      <!--Answer type:-->

      {{ Form::select('s', 
                  array('Open Ended' => 'Open Ended', 'Multiple Choice' => 'Multiple Choice','Scale' => 'Scale','Skill Select' => 'Skill Select'), 
                  $block->answer_type,
                  array(
                    'id' => "block-answer-type-$block->id",
                    'class' => "form-control hidden",
                    'onchange' => "question_options($block->id)"
                    )
                ) }}
    <div class="question-type" id='question-open-{{$block->id}}' 
         @if(trim($block->answer_type) !='Open Ended')
         style='display:none'
         @endif
         >
         How long is the answer?
         <input
            @if($block->scale_min_text=='one')
            checked="checked"
            @endif
            type='radio' name='open-length-{{$block->id}}' class='open-length-{{$block->id}}' id='open-length-{{$block->id}}-one' 
           value='one' /> <label for="open-length-{{$block->id}}-one">One Line</label>
        
         <input 
              @if($block->scale_min_text=='short')
            checked="checked"
            @endif
            type='radio' name='open-length-{{$block->id}}' class='open-length-{{$block->id}}' id='open-length-{{$block->id}}-short' 
           value='short' /> <label for="open-length-{{$block->id}}-short">Short</label>
         
         <input 
              @if($block->scale_min_text=='essay')
            checked="checked"
            @endif
            type='radio' name='open-length-{{$block->id}}' class='open-length-{{$block->id}}' id='open-length-{{$block->id}}-essay' 
           value='essay' /> <label for="open-length-{{$block->id}}-essay">Essay</label>
         <br />
         {{Form::checkbox('c','1',$block->required,array('id'=> "block-answer-required-$block->id")) }}
         <label for="block-answer-required-{{$block->id}}">Required</label>      
  </div>
    <div class="question-type" id='question-skill-{{$block->id}}' 
         @if($block->answer_type!='Skill Select')
         style='display:none'
         @endif
         >
          Allow user to select a minimum and <input type='text' class='form-control' id='block-answer-min-skill-{{$block->id}}' value='{{$block->minimum_choices}}' style='display:inline; width:50px;' /> maximum of 
        <input type='text' class='form-control' id='block-answer-max-skill-{{$block->id}}' value='{{$block->maximum_choices}}' style='display:inline; width:50px;' /> skills.
  </div>
    <div class="question-type" id='question-options-{{$block->id}}' 
         @if($block->answer_type!='Multiple Choice')
         style='display:none'
         @endif
         >
        Allow user to select a minimum and <input type='text' class='form-control' id='block-answer-min-choice-{{$block->id}}' value='{{$block->minimum_choices}}' style='display:inline; width:50px;' /> maximum of 
        <input type='text' class='form-control' id='block-answer-max-choice-{{$block->id}}' value='{{$block->maximum_choices}}' style='display:inline; width:50px;' /> choices.
        <br />Options
        <!--<button class='btn btn-primary btn-sm' onclick='add_question_option({{$block->id}})'><i class='glyphicon glyphicon-plus'></i></button><br />-->
        <div class='options-holder' id='options-holder-{{$block->id}}'>
            @if($block->choices !='')
            <?php 
            $j = 0;$choices = json_decode($block->choices); ?>
            @foreach($choices as $choice)
            <?php
                ++$j;
                $id = 'option-' . time() . rand(100, 999); ?>
            {{-- '<div id="'.$id.'"><div class="input-group"><textarea placeholder="Option" class="mc-textarea option-input">'.br2nl($choice).'\
            </textarea><span class="input-group-btn"><button class="btn btn-danger" type="button" onclick="remove_option(\''.$id.'\')">\n\
            <i class="glyphicon glyphicon-minus"></i></button></span></div><!-- /input-group --><br /></div><!-- /.col-lg-6 -->' --}}
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
              <button class="normalbutton" onclick='add_question_option({{$block->id}})'><span>Add Option</span></button>
            </div>
          </div>
    </div>
    <div class="question-type" id='question-scale-{{$block->id}}'
         @if($block->answer_type!='Scale')
         style='display:none'
         @endif
         >
        Options Per Page <input type='text' class='form-control' id='block-answer-scale-per-tab-{{$block->id}}' value='{{$block->minimum_choices}}'  placeholder="5" />
        Min Value <input type='text' class='form-control' id='block-answer-scale-min-{{$block->id}}' value='{{$block->scale_min}}' style='display:inline; width:50px;' placeholder="E.g. 1" />
        Min Text <input type='text' class='form-control' id='block-answer-scale-min-text{{$block->id}}' value='{{$block->scale_min_text}}' style='display:inline; width:200px;' placeholder="E.g. Not so important" />
        Max Value <input type='text' class='form-control' id='block-answer-scale-max-{{$block->id}}' value='{{$block->scale_max}}' style='display:inline; width:50px;' placeholder="E.g. 10" />
        Max Text <input type='text' class='form-control' id='block-answer-scale-max-text{{$block->id}}' value='{{$block->scale_max_text}}' style='display:inline; width:200px;' placeholder="E.g. Very important" />
        <br />Entries 
        
        <div class='scale-options-holder' id='scale-options-holder-{{$block->id}}'>
            @if($block->scale_entries !='')
            <?php
                $j = 0;
                $choices = json_decode($block->scale_entries); ?>
            @foreach($choices as $choice)
            <?php 
                ++$j;
                $id = 'scale-option-' . time() . rand(100, 999); ?>
            {{-- '<div id="'.$id.'"><div class="input-group"><input type="text" placeholder="Option" value="'.$choice.'" class="form-control option-input"><span class="input-group-btn"><button class="btn btn-danger" type="button" onclick="remove_option(\''.$id.'\')"><i class="glyphicon glyphicon-minus"></i></button></span></div><!-- /input-group --><br /></div><!-- /.col-lg-6 -->' --}}
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
    </div>
      
      <button class='btn btn-danger save-btn' onclick='save_question_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
      
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             


{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>
