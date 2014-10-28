<?php
    $lists = array();
    $skills= DB::table('skills')->get();
    foreach($skills as $s){
        $lists[$s->id] = $s->type;
    }
?>
<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <span id="block-title-span-{{$block->id}}">[#{{$block->id}}] <!--{{($block->title=='') ? '[Title Not Set]' : $block->title}}-->
          @if(trim($block->answer_type) =='Open Ended' || $block->answer_type=='')
              Sortable List
          @else
              Sorted Answers
          @endif
      </span>
      {{View::make('lessons.block_buttons')->withBlock($block)}}
  </div>
  <div class="panel-body">
       <input type="text" class="form-control" id="block-title-{{$block->id}}" value="{{$block->title}}" placeholder="Title" /><br />
       <input type="text" class="form-control" id="block-subtitle-{{$block->id}}" value="{{$block->subtitle}}" placeholder="Sorting Instructions" /><br />
      @if(trim($block->answer_type) =='Open Ended' || $block->answer_type=='')
            Options List:
            {{ Form::select('s', 
                       $lists, 
                        $block->skill_type,
                        array(
                          'id' => "options-list-$block->id",
                          'class' => "form-inline"
                          )
                      ) }}
        @else
            <?php $lessons = Lesson::where('program_id', Session::get('program_id'))->orderBy('chapter_ord','asc')->orderBy('ord','asc')->get();?>
            <select class="swap_select" id="options-list-swap-{{$block->id}}">
                <option value='0'>Select Previous Answer</option>
                @foreach($lessons as $l)
                <?php
                $blocks =  DB::select( DB::raw("SELECT * FROM `blocks`  WHERE  (`lesson_id` = '$l->id' AND `type`='question' AND `answer_type` = 'Open Ended') ") ) ;     // OR (`lesson_id` = '$l->id' AND `type`='scalable')
                
                if($blocks!=null && count($blocks) > 0){ 
                ?>
                    <optgroup label="{{$l->title}}" data-skill-type=''>
                        @foreach($blocks as $b)
                              <option title="ASD" value="{{$b->id}}" data-skill-type=''>{{$b->title}}</option>
                        @endforeach
                    </optgroup>
                    <?php
                   }
                    ?>
                @endforeach
            </select>
            <button class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-backward" onclick="remove_from({{$block->id}})"></i></button>
            <button class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-forward" onclick="add_to({{$block->id}})"></i></button>
            <select class="swap_select swap_final" multiple="true" id="options-list-final-{{$block->id}}">   
                <?php
                    if($block->choices=='') $options = array();
                    else $options = json_decode($block->choices);
                ?>
                @if(count($options)> 0)
                    @foreach($options as $o)
                        <option value='{{$o->block_id}}'>{{$o->label}}</option>
                    @endforeach
                @endif
            </select><br />
        @endif
      <br /> Allow user to select a minimum and <input type='text' class='form-control' id='block-answer-min-skill-{{$block->id}}' value='{{$block->minimum_choices}}' style='display:inline; width:50px;' /> maximum of 
        <input type='text' class='form-control' id='block-answer-max-skill-{{$block->id}}' value='{{$block->maximum_choices}}' style='display:inline; width:50px;' /> options.
        <br />
        Sorting Top Text <input type='text' class='form-control' id='block-answer-scale-max-text{{$block->id}}' value='{{$block->scale_max_text}}' style='display:inline; width:200px;' placeholder="E.g. Most important" />
        Sorting Bottom Text <input type='text' class='form-control' id='block-answer-scale-min-text{{$block->id}}' value='{{$block->scale_min_text}}' style='display:inline; width:200px;' placeholder="E.g. least important" />
        <br />


      <button class='btn btn-danger save-btn' onclick='save_sortable_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
       
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>