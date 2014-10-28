<div class="reply_area2">
    @if($comments != array())
            <?php
            /*<!--if($comments->count()>0 && ($comments->first()->user_id==Auth::user()->id ||  $comments->first()->admin_id==Auth::user()->id))-->*/
            ?>
            @if($comments->count()>0 && 
            ($comments->first()->posted_by=='admin' && admin()) ||  ($comments->first()->posted_by=='user' && !admin()) )
<!--               <button class="reply-comments-{{$answer->id}} buttons purple-button short-buttons comment-btn edit-comment"  
                       type="button" onclick="show_edit_form({{$answer->id}},{{$comments->first()->id}})">Edit Comment</button>-->
                <div class="blue-bg reply-bar reply-bar-{{$answer->id}}">
                    <a 
                       onclick='edit_comment(event, {{$answer->id}}, {{$comments->first()->id}})'
                       class="load-more reply-comments-{{$answer->id}}">Edit Comment</a>
                </div>
            @else
                @if($comments->count()>0 || admin())
<!--                    <button class="reply-comments-{{$answer->id}} buttons purple-button short-buttons comment-btn"  type="button" 
                            onclick="show_reply_form({{$answer->id}})">Reply</button>-->
                <div class="blue-bg reply-bar reply-bar-{{$answer->id}}">
                    <a onclick="comment_reply({{$answer->id}})" class="load-more reply-comments-{{$answer->id}}">Reply</a>
                </div>
                @endif
            @endif
    @endif
</div>