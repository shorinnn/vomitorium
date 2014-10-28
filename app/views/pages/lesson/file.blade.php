<div class="text-block file-block">
    <img class='format-icon' src="{{format_icon($block->text)}}" align='left' />
    <div class='download-icon'>
        <a href='{{url('assets/downloads/'.$block->text)}}' target='_blank'>
        <img src="{{url('assets/img/download-icon.png')}}"  /><br />
        Click Here To <br />Download
        </a>
    </div>
    <p class='file-content'><b>{{$block->title}}</b><br />
    {{$block->subtitle}}
    </p>
    <br />
    <br class='clear_fix' />
    
     
</div>