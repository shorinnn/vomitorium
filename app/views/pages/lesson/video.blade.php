<div class="text-block text-center">    @if(trim($block->title)!='')
    <h2>{{$block->title}}</h2>
    @endif
    <?php
        $text = parse_tags($block->text);
    ?>
    {{$text}}
</div>