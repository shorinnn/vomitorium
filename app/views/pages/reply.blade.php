<div class="reply_area2">
    @if($comments != array())
            @if($comments->count() > 0)
                @if(($comments->first()->posted_by=='admin' && admin() ) || ( $comments->first()->posted_by=='user' && !admin() ))
                    <div class="blue-bg reply-bar reply-bar-{{$answer->id}}">
                        <a 
                           onclick='edit_comment(event, {{$answer->id}}, {{$comments->first()->id}})'
                           class="load-more reply-comments-{{$answer->id}}">Edit Comment</a>
                    </div>
                @else
                    <div class="blue-bg reply-bar reply-bar-{{$answer->id}}">
                        <a onclick="comment_reply({{$answer->id}})" class="load-more reply-comments-{{$answer->id}}">Reply</a>
                    </div>
                @endif
            @else
                @if($comments->count()>0 || admin())
                    <div class="blue-bg reply-bar reply-bar-{{$answer->id}}">
                        <a onclick="comment_reply({{$answer->id}})" class="load-more reply-comments-{{$answer->id}}">Reply</a>
                    </div>
                @endif
            @endif
    @endif
</div>