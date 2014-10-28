@foreach($remarks as $remark)
<?php
if($remark->admin_reply==1) $c = User::find($remark->admin_id);
else $c = User::find($remark->user_id);
?>
<a name="remark-{{$remark->id}}"></a>
<div id='remark-{{$remark->id}}' class='remark-box clearfix coach-box message-box 
     @if($remark->admin_reply==1)
         greenyellow-coach-box 
     @else
         cyan-coach-box
     @endif'> 
    
    <h3>{{$c->username}}  {{format_date($remark->created_at)}}, {{$remark->created_at->diffForHumans()}}</h3>
    @if(!admin())
        @if($remark->read==0)
             <button type='button' id="mark-remark-read-{{$remark->id}}"
                     class="buttons cyan-button short-buttons" onclick="mark_remark_read({{$remark->id}})">Mark As Read</button>               
        @endif
    @else
        @if($remark->attended==0 && $remark->admin_reply==0)
             <button type='button' id="mark-remark-read-{{$remark->id}}"
                     class="unattended buttons cyan-button short-buttons" onclick="mark_remark_attended({{$remark->id}})">Mark As Attended</button>               
        @endif
    @endif
    
     @if($c->avatar=='')
    <img class="pull-left discussion-thumb" src="http://placehold.it/156x156&text={{$c->username}}" />
    @else
    <img class="pull-left discussion-thumb" src="{{url('assets/img/avatars/'.$c->avatar)}}" />
    @endif
    <span class='content'>{{$remark->remark}}</span>
</div>

@endforeach