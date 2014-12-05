<br />
<p class="green-bg conversations-title">Group Conversation</p>

    @if($total_group_remarks>1)
        <button type='button' 
                data-group-convo="1"
                data-id="{{$current_user->id or 0}}" onclick="load_group_lesson_comments({{$lesson->id}},1)" 
        class="btn btn-default load-group-lesson-comments">
            <img src="http://chicken.imacoa.ch/assets/img/arrow-point.png" alt=""> Load Earlier Messages
        </button><br />
        @endif
<div class="lesson-comments-group">
    <div class="lesson-comments">
        {{ View::make('pages.lesson.remarks')->withRemarks($group_remarks) }}
    </div>
</div>
<div class='remark-post-area-group' style='display:none'>
                <span id='remark-reply-area-group'></span>
                <textarea id="remark_reply_top_group" class="white-textarea summernote_editor"></textarea>

                <button type="button" class="btn btn-default2 message-send" 
                        data-rte='#remark_reply_top_group' data-container='.lesson-comments-group' 
                        data-group-convo="1"
                            onclick="do_remark_reply(event, {{$lesson->id}})">Send</button>
            </div>
            <br class='clearfix clear_fix' />
            
@if(!Auth::guest() && $group_remarks->count() > 0)
    @if($group_remarks[$group_remarks->count()-1]->user_id == Auth::user()->id)
        <button type='button' class='btn btn-default force-edit-remark' onclick='force_edit(".lesson-comments-group")'>Edit</button>
    @else
        <button style='display:none' type='button' class='btn btn-default force-edit-remark' onclick='force_edit(".lesson-comments-group")'>Edit</button>
        <button type='button' class='btn btn-default show-remark-reply' onclick='show_remark_reply(".remark-post-area-group")'>Reply</button>
    @endif        
@else
    <button style='display:none' type='button' class='btn btn-default force-edit-remark' onclick='force_edit(".lesson-comments-group")'>Edit</button>
    <button type='button' class='btn btn-default show-remark-reply' onclick='show_remark_reply(".remark-post-area-group")'>Start The Conversation</button>
@endif
<span class='clearfix clear_fix'></span>