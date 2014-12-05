@if($answer!=null)
    @if(admin())

    <section class=' new-submission'>
            <div class=''>
                <article class='col-md-12'>
                    @if($answer->attended==0)
                    <span id="red-btn-{{$answer->id}}"class="red-button buttons new-btn unread unread-submission pull-right"><i class="glyphicon glyphicon-exclamation-sign "></i> </span>
                        <button id="mark-s-read-{{$answer->id}}" type="button" class="buttons greenyellow-button short-buttons pull-right" onclick="mark_submission_attended({{$answer->id}})"><i class="glyphicon glyphicon-ok"></i> Mark as reviewed</button>
                    @else
                        <span class="red-button buttons new-btn unread unread-submission pull-right invisible"></span>
                        <button id="mark-s-read-{{$answer->id}}" type="button" class="buttons red-button short-buttons pull-right" onclick="mark_submission_unattended({{$answer->id}})">Mark as not yet reviewed</button>
                    @endif
                </article>
            </div>
            <br class="clearfix" />
        </section>
    @endif
@endif 