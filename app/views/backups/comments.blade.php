<?php
    if(!isset($single_object)) $comments = $comments->reverse();
?>

    @foreach($comments as $c)
    <!--
        <div class="panel panel-default block" id='comment-{{$c->id}}'>
            @if($c->admin_id>0)
                @if($c->read==1 || admin())
                    <div class="panel-heading admin_comment">
                @else
                    <div class="panel-heading admin_comment new_comment">
                @endif
            @else
                @if($c->attended==1)
                    <div class="panel-heading">
                @else
                    <div class="panel-heading new_comment">
                @endif
            @endif
            
                <a href="#">{{$c->username()}}</a> {{format_date($c->created_at)}}, {{$c->created_at->diffForHumans()}}
                @if(!admin())
                    @if($c->read==0)
                        <button id="mark-read-{{$c->id}}" type="button" class="btn btn-primary btn-xs pull-right" onclick="mark_read({{$c->id}},{{$c->block_answer_id}})">Mark as read</button>
                    @endif
                @else
                    @if($c->attended==0)
                        <button id="mark-read-{{$c->id}}" type="button" class="btn btn-primary btn-xs pull-right" onclick="mark_attended({{$c->id}},{{$c->block_answer_id}})">Mark as attended</button>
                    @elseif($c->attended=1 && $c->admin_id==0)
                        <button id="mark-read-{{$c->id}}" type="button" class="btn btn-primary btn-xs pull-right" onclick="mark_unattended({{$c->id}},{{$c->block_answer_id}})">Mark as unattended</button>
                    @endif
                @endif
            </div>
            <div class="panel-body">
                <div class='comment-content'>
                    @if($c->user_avatar()=='')
                    <img class="pull-left discussion-thumb" src="http://placehold.it/80x80&text={{$c->username()}}" />
                    @else
                    <img class="pull-left discussion-thumb" src="{{url('assets/img/avatars/'.$c->user_avatar())}}" />
                    @endif
                    <span class='content'>{{{$c->reply}}}</span>
                </div>
            </div>
        </div>
                -->
                <!---->
               
            <?php
                if($c->admin_id>0){
                    if($c->read==1 || admin()) $class = "greenyellow-coach-box";
                    else  $class = "greenyellow-coach-box purple-border";
                }
                else{
                    if($c->attended==1 || !admin())$class = "cyan-coach-box";
                    else $class="cyan-coach-box purple-border";
                }
            ?>
                <div class="clearfix coach-box message-box {{$class}}" id='comment-{{$c->id}}'>
                	<h3>{{$c->username()}} {{format_date($c->created_at)}}, {{$c->created_at->diffForHumans()}}</h3>
                    @if($c->user_avatar()=='')
                        <img class="pull-left discussion-thumb" src="http://placehold.it/80x80&text={{$c->username()}}" />
                    @else
                        <img class="pull-left discussion-thumb" src="{{url('assets/img/avatars/'.$c->user_avatar())}}" />
                    @endif
                    <span class='content'>{{{$c->reply}}}</span>
                    
                 @if(!admin())
                    @if($c->read==0)
                        <button id="mark-read-{{$c->id}}" type="button" class="buttons purple-button short-buttons" onclick="mark_read({{$c->id}},{{$c->block_answer_id}})">Mark As Read</button>
                    @endif
                @else
                    @if($c->attended==0 && $c->admin_id==0)
                        <button id="mark-read-{{$c->id}}" type="button" class="buttons purple-button short-buttons" onclick="mark_attended({{$c->id}},{{$c->block_answer_id}})">Mark as attended</button>
                    @elseif($c->attended=1 && $c->admin_id==0)
                        <button id="mark-read-{{$c->id}}" type="button" class="buttons purple-button short-buttons" onclick="mark_unattended({{$c->id}},{{$c->block_answer_id}})">Mark as unattended</button>
                    @endif
                @endif
                </div>
        @endforeach

       