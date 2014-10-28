<div class="block_div" id='block-{{$block->id}}'>
<h2>{{$block->title}}</h2>


<?php $blocks = Block::where('category_id',$block->category_id)->where('type','!=','category')->get();?>

@foreach($blocks as $b)
<?php
    $user_id = (Session::has('user_id')) ? Session::get('user_id') : Auth::user()->id;
    $answer = Block_answer::where('block_id',$b->id)->where('user_id', $user_id)->first();
    if($answer!=null){?>
       {{$answer->answer}}<br />
   <?php } ?>
@endforeach  
</div>