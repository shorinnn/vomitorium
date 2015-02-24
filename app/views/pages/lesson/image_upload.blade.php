<?php
    $img_url = '';
    $answer = NULL;
    if(Auth::check()){
        $answer = Block_answer::where('user_id',Auth::user()->id)->where('block_id', $block->id)->first();
    }
    if($answer!=null){
        $img_url = $answer->answer;
    }
?>
<div class="block upload-block">    
    @if(trim($block->title)!='')
    <h2>{{$block->title}}</h2>
    <br />
    @endif
    <div class='uploaded_img current_img_{{$block->id}}'>
        @if($img_url!='')
            <img src='{{url("assets/uploads/$img_url")}}' />
        @endif
    </div>
    
    @if(Auth::check())
        <input type="file" name="file_{{$block->id}}" id="file_{{$block->id}}" onchange="image_selected({{$block->id}})"/>
        <input type="button" class="btn btn-sm btn-primary upload-btn upload-btn-{{$block->id}}" value="Choose File" onclick="upload_dialog('#file_{{$block->id}}')"/>
        <p class="filename-holder filename-holder-{{$block->id}}"></p>
        <input type='button' class="btn btn-default" onclick="upload_user_image({{$block->id}})" value='Upload' />
    @endif
</div>