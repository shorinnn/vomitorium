<?php
    $file_url = '';
    $answer = Block_answer::where('user_id',Auth::user()->id)->where('block_id', $block->id)->first();
    if($answer!=null){
        $file_url = $answer->answer;
    }
?>
<div class="block upload-block">    
    @if(trim($block->title)!='')
    <h2>{{$block->title}}</h2>
    <br />
    @endif
    <div class='uploaded_file uploaded_file_{{$block->id}}'>
        @if($file_url!='')
            <a href='{{url("assets/uploads/$file_url")}}' target='_blank'>[Download - {{human_filesize(filesize("assets/uploads/$file_url"))}}]</a>
        @endif
    </div>
    
    
    <input type="file" name="file_{{$block->id}}" id="file_{{$block->id}}" onchange="image_selected({{$block->id}})"/>
    <input type="button" class="btn btn-sm btn-primary upload-btn upload-btn-{{$block->id}}" value="Choose File" onclick="upload_dialog('#file_{{$block->id}}')"/>
    <p class="filename-holder filename-holder-{{$block->id}}"></p>
    <input type='button' class="btn btn-default" onclick="upload_user_file({{$block->id}})" value='Upload' />
</div>