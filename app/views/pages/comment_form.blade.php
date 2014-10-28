@if($total>0 || admin())
        @if($unread==='do not show')
        <br />
<section class='container'>
        <div class='container'>

            <article class='col-md-12'>
                @if(admin())
                    <span class="unread-{{$answer->block_id}} red-button buttons new-btn unread unread-comment">{{$unread}} <i class="glyphicon glyphicon-comment"></i></span>
                @else
                    <span class="unread-{{$answer->block_id}} red-button buttons new-btn unread unread-comment">{{$unread}} <i class="glyphicon glyphicon-comment"></i></span>
                @endif
            </article>
        </div>
    </section>
@endif

    <div id="comments-{{$answer->id}}"  class='comments-area'>
        @if($total>0 && Conversation::where('block_answer_id', $answer->id)->count() > 2)
<!--            <button class="btn btn-link load-comments-{{$answer->id}}" id="load-comments-{{$answer->id}}" type="button" onclick="load_messages({{$answer->id}},2)">
                <i class='glyphicon glyphicon-chevron-up'></i> Load Earlier Messages <i class='glyphicon glyphicon-chevron-up'></i></button>-->
            
        <div class="blue-bg load-comments-{{$answer->id}}">
            <a id="load-comments-{{$answer->id}}" onclick="load_messages({{$answer->id}},2)" class="load-more "><img src="{{url('assets/img/arrow-point.png')}}" alt=""> Load more</a>
        </div>
        @endif


       {{View::make('pages.lesson.comments')->withComments($comments)}}
       
       <!--
       @if($comments->count()>0 && ($comments->first()->user_id==Auth::user()->id ||  $comments->first()->admin_id==Auth::user()->id))
           <button class="reply-comments-{{$answer->id}} buttons purple-button short-buttons comment-btn edit-comment"  type="button" onclick="show_edit_form({{$answer->id}},{{$comments->first()->id}})">Edit Comment</button>
        @else
            <button class="reply-comments-{{$answer->id}} buttons purple-button short-buttons comment-btn"  type="button" onclick="show_reply_form({{$answer->id}})">Reply</button>
        @endif
       -->
    </div>
@endif