<?php
    if(!isset($single_object)) $comments = $comments->reverse();
?>
@foreach($comments as $c)
        @if( (!admin() && $c->posted_by=='user' ) || (admin() && $c->posted_by=='admin') )
        <div class="convo-box coach-col" id='comment-{{$c->id}}'>
            <div class="left-col">
        @else
        <div class="convo-box student-col" id='comment-{{$c->id}}'>
                <!--<div class="right-col">-->
                <div class="left-col">
        @endif
        @if($c->poster()->avatar=='')
            <img class="img-responsive img-col" src="http://placehold.it/156x156&text={{$c->poster()->username}}" />
        @else
            <img class="img-responsive img-col" src="{{url('assets/img/avatars/'.$c->poster()->avatar)}}" />
        @endif
            <div class="clearfix"></div>
            <h3>{{$c->poster()->first_name.' '.$c->poster()->last_name}}</h3>
            <h4>
                @if($c->posted_by =='admin') 
                    Coach
                @else
                    Student
                @endif
            </h4>
        </div><!--left-col ends-->
        @if( (admin() && $c->posted_by == 'user' ) || (!admin() && $c->posted_by=='admin') )
            <div class="coach-textarea">
        @else
            <div class="student-textarea">
        @endif
            <span class="time pull-left do-tooltip" title='{{format_date($c->updated_at)}}'> 
                {{$c->updated_at->diffForHumans()}}</span>
            <div class="clearfix"></div>
            <span class='text'>{{$c->content}}</span>
            <div class="attachment">
                @if($c->attachments->count() > 0)
                    <br /><span class="time">Attachments</span>
                    @foreach($c->attachments as $a)
                        <p><span><a target='_blank' href="{{url("assets/uploads/attachments/$a->filename")}}">{{$a->orig_name}}</a></span> {{human_filesize(filesize("assets/uploads/attachments/$a->filename"))}}</p>
                    @endforeach
                 @endif
            </div>
            <span class="comment-text-area">
            <ul class="list-unstyled">
                @if(!admin())
                    @if($c->read==0)
                    <li>
                        <a id="mark-read-{{$c->id}}" title="Mark As Read" class="do-tooltip"
                           onclick="mark_read({{$c->id}},{{$c->block_answer_id}})">Mark As Read</a>  
                    </li>
                    @endif
                @else
                    @if($c->attended==0 && $c->posted_by=='user')
                    <li>
                         <a  id="mark-read-{{$c->id}}" title="Mark As Reviewed" class="do-tooltip"
                                 onclick="mark_attended({{$c->id}},{{$c->block_answer_id}})">Mark As Reviewed</a>
                    </li>
                        
                    @endif
                @endif
            </ul>
                </span>
        </div><!--coach-textarea ends-->
        <div class="clearfix"></div>
    </div>
@endforeach