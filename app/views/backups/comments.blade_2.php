<?php
    if(!isset($single_object)) $comments = $comments->reverse();
?>
@foreach($comments as $c)

        @if( (admin() && $c->admin_id==0 ) || (!admin() && $c->admin_id>0) )
        <div class="coach-col" id='comment-{{$c->id}}'>
            <div class="left-col">
        @else
        <div class="student-col" id='comment-{{$c->id}}'>
                <!--<div class="right-col">-->
                <div class="left-col">
        @endif
        
        @if($c->user_avatar()=='')
            <img class="img-responsive img-col" src="http://placehold.it/156x156&text={{$c->user()->username}}" />
        @else
            <img class="img-responsive img-col" src="{{url('assets/img/avatars/'.$c->user_avatar())}}" />
        @endif
            <div class="clearfix"></div>
            <h3>{{$c->user()->first_name.' '.$c->user()->last_name}}</h3>
            <h4>
                @if($c->admin_id > 0) 
                    Coach
                @else
                    Student
                @endif
            </h4>
        </div><!--left-col ends-->
        @if( (admin() && $c->admin_id == 0 ) || (!admin() && $c->admin_id > 0) )
            <div class="coach-textarea">
        @else
            <div class="student-textarea">
        @endif
            <span class="time pull-left do-tooltip" title='{{format_date($c->created_at)}}'> 
                {{$c->created_at->diffForHumans()}}</span>
            <div class="clearfix"></div>
            <span class='text'>{{$c->reply}}</span>
            
            <div class="attachment">
                @if($c->attachments->count() > 0)
                    <h5>ATTACHMENTS</h5>
                    @foreach($c->attachments as $a)
                        <p><span><a target='_blank' href="{{url("assets/uploads/attachments/$a->filename")}}">{{$a->orig_name}}</a></span> {{human_filesize(filesize("assets/uploads/attachments/$a->filename"))}}</p>
                    @endforeach
                 @endif
            </div>
           
            <ul class="list-unstyled option-box">
                @if(!admin())
                    @if($c->read==0)
                    <li>
                        <a id="mark-read-{{$c->id}}" title="Mark As Read" class="do-tooltip"
                           onclick="mark_read({{$c->id}},{{$c->block_answer_id}})"><i class="glyphicon glyphicon-ok"></i></a>  
                    </li>
                    @endif
                @else
                    @if($c->attended==0 && $c->admin_id==0)
                    <li>
                         <a  id="mark-read-{{$c->id}}" title="Mark As Attended" class="do-tooltip"
                                 onclick="mark_attended({{$c->id}},{{$c->block_answer_id}})"><i class="glyphicon glyphicon-ok"></i></a>
                    </li>
                    @endif
                @endif

            </ul>            

        
        </div><!--coach-textarea ends-->
        <div class="clearfix"></div>
    </div>
            
    
   
@endforeach

       