@if($is_report==0)
<select class="form-control" id="answers_tag">
@else
<select class="form-control" id="answers_tag" onchange="show_tag_val();">
@endif
  <option value='0'>Select Previous Answer</option>
  @foreach($lessons as $l)  
  <?php
         $blocks =  DB::select( DB::raw("SELECT * FROM `blocks`  
             WHERE (`lesson_id` = '$l->id' AND `type`='question')
             OR (`lesson_id` = '$l->id' AND `type`='sortable')
             OR (`lesson_id` = '$l->id' AND `type`='scalable')
             OR (`lesson_id` = '$l->id' AND `type`='image_upload')") ) ;     
  ?>
  
      <optgroup label="{{$l->title}} {{$l->id}}">
          @foreach($blocks as $b)
                <option value="{{$b->id}}">{{$b->title}} </option>
          @endforeach
      </optgroup>
  @endforeach
</select>
<br />
<span id="tag_val"></span>