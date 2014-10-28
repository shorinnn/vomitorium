<div class="text-block">
    @if(trim($block->title)!='')
    <h2>{{$block->title}}</h2>
    @endif
    <?php
        $text = parse_tags($block->text);
    ?>
    {{$text}}
    <span class='clearfix' style='clear:both'></span>
</div>