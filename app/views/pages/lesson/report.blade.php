<div class="text-block report-block">    @if(trim($block->title)!='')
    <h2>{{$block->title}}</h2>
    @endif
    <?php
        $text = parse_tags($block->text);
    ?>
    {{$text}}
    
</div>