<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <div class="col-lg-8">
          <span>[#{{$block->id}}] Previous Answer Block</span>
      </div>
      <div class="col-lg-4">
          {{View::make('lessons.block_buttons')->withBlock($block)}}
      </div>
  </div>
  <div class="panel-body">

      <select class="form-control" id="block-answer-{{$block->id}}">
          <option value='0'>Select Previous Answer</option>
          
          @foreach($lessons as $l)
          <?php
          $blocks =  DB::select( DB::raw("SELECT * FROM `blocks`  WHERE (`lesson_id` = '$l->id' AND `type`='question') OR (`lesson_id` = '$l->id' AND `type`='scalable') ") ) ;     
          ?>
              <optgroup label="{{$l->title}}" data-skill-type=''>
                  <!--@ foreach($l->blocks()->where('type','question')->orWhere('type','sortable')->get() as $b)-->
                  @foreach($blocks as $b)
                      @if($b->answer_type != 'Skill Select')
                            @if($block->answer_id==$b->id)
                              <option value="{{$b->id}}" data-skill-type='' selected='selected'>{{$b->title}}</option>
                            @else
                            <option value="{{$b->id}}" data-skill-type=''>{{$b->title}}</option>
                            @endif
                      @else
                           @if($block->answer_id==$b->id && $block->skill_type=='functional')
                           <option value="{{$b->id}}" data-skill-type='functional' selected="selected">{{$b->title}} -- Functional</option>
                           @else
                              <option value="{{$b->id}}" data-skill-type='functional'>{{$b->title}} -- Functional</option>
                           @endif
                           
                           @if($block->answer_id==$b->id && $block->skill_type=='personality')
                          <option value="{{$b->id}}" data-skill-type='personality' selected='selected'>{{$b->title}} -- Personality</option>
                           @else 
                          <option value="{{$b->id}}" data-skill-type='personality'>{{$b->title}} -- Personality</option>
                          @endif
                      @endif
                  @endforeach
              </optgroup>
          @endforeach
      </select>
       {{ Form::select('s', 
                  array('0' => 'Standalone Block', '1' => 'Included in section'), 
                  $block->in_section,
                  array(
                    'id' => "in_section-$block->id",
                    'class' => "form-inline"
                    )
                ) }}

      <button class='btn btn-danger save-btn' onclick='save_answer_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
      
      
      {{View::make('lessons.categories')->withBlock($block)}}
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>