@if($answer!=null)
    @if(admin())
    <!--
        <section class='container new-submission'>
            <div class='container'>
                <article class='col-md-12'>
                    @if($answer->attended==0)
                    <span class="red-button buttons new-btn unread unread-submission noti"><i class="glyphicon glyphicon-exclamation-sign"></i> </span>
                        <button id="mark-s-read-{{$answer->id}}" type="button" class="buttons greenyellow-button short-buttons noti noti-mark" onclick="mark_submission_attended({{$answer->id}})"><i class="glyphicon glyphicon-ok"></i> Mark as attended</button>
                    @else
                        <span class="pink-button buttons new-btn unread unread-submission invisible noti"></span>
                        <button id="mark-s-read-{{$answer->id}}" type="button" class="buttons red-button short-buttons noti noti-mark" onclick="mark_submission_unattended({{$answer->id}})">Mark as unattended</button>
                    @endif
                </article>
            </div>
            <br class="clearfix" />
        </section>
    -->
    <section class=' new-submission'>
            <div class=''>
                <article class='col-md-12'>
                    @if($answer->attended==0)
                    <span id="red-btn-{{$answer->id}}"class="red-button buttons new-btn unread unread-submission pull-right"><i class="glyphicon glyphicon-exclamation-sign "></i> </span>
                        <button id="mark-s-read-{{$answer->id}}" type="button" class="buttons greenyellow-button short-buttons pull-right" onclick="mark_submission_attended({{$answer->id}})"><i class="glyphicon glyphicon-ok"></i> Mark as attended</button>
                    @else
                        <span class="red-button buttons new-btn unread unread-submission pull-right invisible"></span>
                        <button id="mark-s-read-{{$answer->id}}" type="button" class="buttons red-button short-buttons pull-right" onclick="mark_submission_unattended({{$answer->id}})">Mark as unattended</button>
                    @endif
                </article>
            </div>
            <br class="clearfix" />
        </section>
    @endif
@endif 