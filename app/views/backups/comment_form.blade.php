@if($unread>0)
<!-- <br />
<p class="text-right unattended_item ">
    @if(admin())
        <i class="glyphicon glyphicon-info-sign"></i> You have {{$unread}} unattended {{singplural($unread, 'messages')}}!
    @else
        <i class="glyphicon glyphicon-info-sign"></i> You have {{$unread}} unread {{singplural($unread, 'messages')}}!
    @endif
</p> 

<section class='container'>
        <div class='container'>
            <article class='col-md-12'>
                @if(admin())
                    <a  style="margin-bottom:-20px;" class="pink-button buttons long-buttons" href="#">{{$unread}} unattended {{singplural($unread, 'messages')}}!</a>
                @else
                    <a class="pink-button buttons long-buttons" href="#">{{$unread}} unread {{singplural($unread, 'messages')}}!</a>
                @endif
            </article>
        </div>
    </section>-->
@endif


@if($total>0 || admin())
        @if($unread>0)
        <br />
<section class='container'>
        <div class='container'>
            <article class='col-md-12'>
                @if(admin())
                    <a class="pink-button buttons long-buttons unread" href="#">{{$unread}} unattended {{singplural($unread, 'messages')}}!</a>
                @else
                    <a class="pink-button buttons long-buttons unread" href="#">{{$unread}} unread {{singplural($unread, 'messages')}}!</a>
                @endif
            </article>
        </div>
    </section>
@endif
    <div id="comments-{{$answer->id}}"  class='comments-area'>
        @if($total>0)
            <button class="btn btn-link load-comments-{{$answer->id}}" id="load-comments-{{$answer->id}}" type="button" onclick="load_messages({{$answer->id}},2)">Load Earlier Messages</button>
        @endif


       {{View::make('pages.lesson.comments')->withComments($comments)}}
       
        @if($comments->count()>0 && ($comments->first()->user_id==Auth::user()->id ||  $comments->first()->admin_id==Auth::user()->id))
            <button class="reply-comments-{{$answer->id}} buttons purple-button short-buttons comment-btn"  type="button" onclick="show_edit_form({{$answer->id}},{{$comments->first()->id}})">Edit Comment</button>
        @else
            <button class="reply-comments-{{$answer->id}} buttons purple-button short-buttons comment-btn"  type="button" onclick="show_reply_form({{$answer->id}})">Reply</button>
        @endif
    </div>
@endif