<div class="block_div skill-block" id='block-{{$block->id}}'>
    <div class="q-icon">Q</div>
    <h2>Top {{$block->top_skill_type}} Skills</h2>
    @if(trim($block->subtitle)!='')
        <h4>{{$block->subtitle}}</h4>
    @endif
    
    <?php
        $skills = Block::top_skills($block->top_skill_type, $block->top_skill_count);
    ?>
    @foreach($skills as $skill => $count)
        {{$skill}}<br />
    @endforeach
</div>