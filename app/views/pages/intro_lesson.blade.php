<?php
$page_has_submit = false;
$prev_block = '';
$first_in_section = $lesson->blocks()->where('in_section', 1)->orderBy('ord', 'asc')->get();
if ($first_in_section->count() == 0)
    $first_in_section = 99999;
else
    $first_in_section = $first_in_section->first()->ord;
$page_has_scale = '';
$total_answers = 0;
// display blocks that are not included in sections first
foreach ($lesson->blocks()->where('ord', '<=', $first_in_section)->where('in_section', 0)->orderBy('ord', 'ASC')->get() as $block) {
    if ($block->type == 'text') {
        $prev_block = View::make('pages.lesson.text')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'report') {
        $prev_block = View::make('pages.lesson.report')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'video') {
        $prev_block = View::make('pages.lesson.video')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'file') {
        $prev_block = View::make('pages.lesson.file')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'top_skills' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.top_skills')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'dynamic' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.dynamic')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'category' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.category')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'sortable' && !Auth::guest()) {
        //$page_has_scale = 'hidden';
        $prev_block = View::make('pages.lesson.sortable')->withBlock($block);
        echo $prev_block;
        $page_has_submit = true;
        $total_answers++;
    } else if ($block->type == 'image_upload') {
        $prev_block = View::make('pages.lesson.image_upload')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else if ($block->type == 'file_upload') {
        $prev_block = View::make('pages.lesson.file_upload')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else if ($block->type == 'answer') {
        $page_has_submit = true;
        if (!Auth::guest()) {
            $answer = Block::find($block->answer_id);
            if ($answer != null) {
                if ($answer->answer_type == 'Scale') {
                    $prev_block = View::make('pages.lesson.previous-scale')->withBlock($block);
                    echo $prev_block;
                } else if ($answer->answer_type == 'Skill Select') {
                    $prev_block = View::make('pages.lesson.previous-skill')->withBlock($block);
                    echo $prev_block;
                } else {
                    $prev_block = View::make('pages.lesson.answer')->withBlock($block);
                    echo $prev_block;
                }
            }
        }
    } else {
        $page_has_submit = true;
        $view = Str::slug($block->answer_type);
        if ($block->answer_type == 'Scale')
            $page_has_scale = 'hidden';
    }
}

// display blocks that are included in sections
$section_count = 0;
$section_open = false;
$i = 0;
$total_sections = 0;
$created_sections = array();
foreach ($lesson->blocks()->where('in_section', 1)->orderBy('ord', 'ASC')->get() as $block) {
    ++$i;
    if ($section_count == 0 && !in_array($total_sections, $created_sections)) {
        $created_sections[] = $total_sections;
        if ($i == 1)
            echo "<div data-section='$total_sections' class='blocks_section section-$total_sections'>";
        else
            echo "<div data-section='$total_sections' class='blocks_section hidden_section section-$total_sections'>";
        $section_open = true;
    }
    if ($block->type == 'text') {
        $prev_block = View::make('pages.lesson.text')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'report') {
        $prev_block = View::make('pages.lesson.report')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'video') {
        $prev_block = View::make('pages.lesson.video')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'file') {
        $prev_block = View::make('pages.lesson.file')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'top_skills' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.top_skills')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'dynamic' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.dynamic')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'category' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.category')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'sortable' && !Auth::guest()) {
        $page_has_submit = true;
        //$page_has_scale = 'hidden';
        $prev_block = View::make('pages.lesson.sortable')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else if ($block->type == 'answer' && !Auth::guest()) {
        $page_has_submit = true;
        if (!Auth::guest()) {
            $answer = Block::find($block->answer_id);
            if ($answer != null) {
                if ($answer->answer_type == 'Scale') {
                    $prev_block = View::make('pages.lesson.previous-scale')->withBlock($block);
                    echo $prev_block;
                } else if ($answer->answer_type == 'Skill Select') {
                    $prev_block = View::make('pages.lesson.previous-skill')->withBlock($block);
                    echo $prev_block;
                } else {
                    $prev_block = View::make('pages.lesson.answer')->withBlock($block);
                    echo $prev_block;
                }
            }
        }
    } else if ($block->type == 'image_upload') {
        $prev_block = View::make('pages.lesson.image_upload')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else if ($block->type == 'file_upload') {
        $prev_block = View::make('pages.lesson.file_upload')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else {
        $page_has_submit = true;
        $view = Str::slug($block->answer_type);
        if ($block->answer_type == 'Scale')
            $page_has_scale = 'hidden';
    }
    if ($section_count == $lesson->section_capacity - 1) {
        $total_sections++;
        echo "</div>";
        $section_open = false;
        $section_count = -1;
    }
    $section_count++;
}
if ($section_open) {
    if ($section_count > 0)
        $total_sections++;
    echo '</div>';
}

?>   
<br style='clear:both' />
<!-- after section blocks -->
<?php
$last_in_section = $lesson->blocks()->where('in_section', 1)->orderBy('ord', 'desc')->get();
if ($last_in_section->count() == 0)
    $last_in_section = 99999999;
else
    $last_in_section = $last_in_section->first()->ord;
// display blocks that are not included in sections first
foreach ($lesson->blocks()->where('ord', '>=', $last_in_section)->where('in_section', 0)->orderBy('ord', 'ASC')->get() as $block) {
    if ($block->type == 'text') {
        $prev_block = View::make('pages.lesson.text')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'report') {
        $prev_block = View::make('pages.lesson.report')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'video') {
        $prev_block = View::make('pages.lesson.video')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'file') {
        $prev_block = View::make('pages.lesson.file')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'top_skills' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.top_skills')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'dynamic' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.dynamic')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'category' && !Auth::guest()) {
        $prev_block = View::make('pages.lesson.category')->withBlock($block);
        echo $prev_block;
    } else if ($block->type == 'sortable' && !Auth::guest()) {
        //$page_has_scale = 'hidden';
        $prev_block = View::make('pages.lesson.sortable')->withBlock($block);
        echo $prev_block;
        $page_has_submit = true;
        $total_answers++;
    } else if ($block->type == 'image_upload') {
        $prev_block = View::make('pages.lesson.image_upload')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else if ($block->type == 'file_upload') {
        $prev_block = View::make('pages.lesson.file_upload')->withBlock($block);
        echo $prev_block;
        $total_answers++;
    } else if ($block->type == 'answer') {
        $page_has_submit = true;
        if (!Auth::guest()) {
            $answer = Block::find($block->answer_id);
            if ($answer != null) {
                if ($answer->answer_type == 'Scale') {
                    $prev_block = View::make('pages.lesson.previous-scale')->withBlock($block);
                    echo $prev_block;
                } else if ($answer->answer_type == 'Skill Select') {
                    $prev_block = View::make('pages.lesson.previous-skill')->withBlock($block);
                    echo $prev_block;
                } else {
                    $prev_block = View::make('pages.lesson.answer')->withBlock($block);
                    echo $prev_block;
                }
            }
        }
    } else {
        $page_has_submit = true;
        $view = Str::slug($block->answer_type);
        if ($block->answer_type == 'Scale')
            $page_has_scale = 'hidden';
    }
}
?>
<!--/ after section blocks -->

<br class='clearfix clear_fix' />

