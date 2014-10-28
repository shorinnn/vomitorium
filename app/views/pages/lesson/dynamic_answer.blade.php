<div class="panel-group" id="accordion">
@foreach($blocks as $b)
<?php
    $user_id = (Session::has('user_id')) ? Session::get('user_id') : Auth::user()->id;
    $answer = Block_answer::where('block_id',$b->id)->where('user_id', $user_id)->first();
    if($answer!=null && trim($answer->answer)!=''){?>
        
<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <span class='btn btn-link' onclick="do_slide_toggle('#collapse{{$answer->id}}')">
            <i class="glyphicon glyphicon-resize-small"></i> {{$b->title}}
        </span>
      </h4>
    </div>
    <div id="collapse{{$answer->id}}" class="panel">
      <div class="panel-body">
       {{$answer->answer}}
      </div>
    </div>
  </div>

   <?php } ?>
@endforeach  
</div>