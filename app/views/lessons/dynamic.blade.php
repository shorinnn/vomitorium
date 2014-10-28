<div class="panel panel-success block list-row-{{$block->id}}" id="block-{{$block->id}}">
  <div class="panel-heading">
      <span>[#{{$block->id}}] Conditional Content</span>
      {{View::make('lessons.block_buttons')->withBlock($block)}}
  </div>
  <div class="panel-body">
      This block will list answers based on the selected category.<br />
      Category:<br />
      <?php
        $cat = DB::table('block_categories')->get();
        $cats = array();
        $checked_arr = array();
        try{
            if($block->subtitle!='') $checked_arr = json_decode($block->subtitle);
        }
        catch(Exception $e){
            $checked_arr = array();
        }
        
        foreach($cat as $c){
            $id = "db-$block->id-$c->id";
            $checked = '';
            if(in_array($c->id, $checked_arr)) $checked=' checked="checked" ';
            echo "<input type='checkbox' $checked class='dynamic-category-selected-$block->id' id='$id' value='$c->id' /> <label for='$id'>$c->category</label><br />";
        }
      ?>
       {{ Form::select('s', 
                  array('0' => 'Standalone Block', '1' => 'Included in section'), 
                  $block->in_section,
                  array(
                    'id' => "in_section-$block->id",
                    'class' => "form-inline"
                    )
                ) }}

      <button class='btn btn-danger save-btn' onclick='save_dynamic_block({{$block->id}},"{{action('LessonsController@update_block',array($block->id))}}")'>Save</button>
  </div>             



{{View::make('lessons.add_block_area')->withBlock($block)}}
</div>