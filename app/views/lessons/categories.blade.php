<?php 
        $cats = DB::table('block_categories')->get();
        $values = array();
        $values[0] = 'None';
        foreach($cats as $c){
            $values[$c->id] = $c->category;
        }
      ?>
<br />
<span  class='btn btn-link' onclick='do_slide_toggle("#options-{{$block->id}}")'>Advanced Options</span>
<div id='options-{{$block->id}}' style='display:none'>
    <div class='well' >
        {{ Form::select('s', 
                      array('0' => 'Standalone Block', '1' => 'Included in section'), 
                      $block->in_section,
                      array(
                        'id' => "in_section-$block->id",
                        'class' => "form-control"
                        )
                    ) }}
                    <br />
    Block Category <button type="button" class="btn btn-primary add_category">Add New Category</button>
    {{ Form::select('c', 
                      $values, 
                      $block->category_id,
                      array(
                        'id' => "category-$block->id",
                        'class' => "form-control category"
                        )
                    ) }}
                    <br />

    </div>
</div>