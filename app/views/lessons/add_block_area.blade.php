<div class='add_block_area add_block_area_{{$block->id}}' data-block-id='{{$block->id}}'>
 <button class="btn btn-danger add-element-btn add-element-btn-{{$block->id}}" onclick='add_new_page_element("{{action("LessonsController@add_block",array($block->lesson_id))}}",{{$block->id}})'><i class='glyphicon glyphicon-plus'></i></button>
</div>