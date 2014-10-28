<div class='pull-right'>      
      <button data-toggle='tooltip' title='Minimize/Expand block' class='do-tooltip btn btn-primary btn-sm' onclick='toggle_block("{{"block-$block->id"}}")'><i class="toggle-block-btn glyphicon glyphicon-resize-small"></i></button> 
      <button data-toggle='tooltip' title='Move block' class='do-tooltip btn btn-primary btn-sm' 
              onclick="move_block_at_pos({{$block->id}})">
          <i class="glyphicon glyphicon-resize-vertical"></i></button> 
      <!--
      
      <button data-toggle='tooltip' title='Move block up' class='do-tooltip btn btn-primary btn-sm' 
              onclick="move_block('up','block-{{$block->id}}','{{action('LessonsController@move_block', array( $block->id,'up'))}}')"><i class="glyphicon glyphicon-arrow-up"></i></button> 
      <button data-toggle='tooltip' title='Move block down' class='do-tooltip btn btn-primary btn-sm'
              onclick="move_block('down','block-{{$block->id}}','{{action('LessonsController@move_block', array( $block->id,'down'))}}')"><i class="glyphicon glyphicon-arrow-down"></i></button> 
      -->
<!--      <button data-toggle='tooltip' title='Delete block' class='do-tooltip btn btn-danger btn-sm' 
      onclick="del({{$block->id}},'{{action('LessonsController@remove_block', array( $block->id))}}' )"><i class='glyphicon glyphicon-trash'></i></button> -->
      
      
      <button data-toggle='tooltip' title='Delete block'  class='do-tooltip btn btn-danger btn-warning btn-sm delete-btn' 
        data-target='list-row' data-id='{{$block->id}}' data-url="{{action('LessonsController@remove_block', array( $block->id))}}">
                <i class='glyphicon glyphicon-trash'></i>
      </button>
                
                <div class="clear_fix clearfix"></div>
  </div>