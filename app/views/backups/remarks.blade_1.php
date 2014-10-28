<?php
$i = 1;
$total = is_array($remarks)? count($remarks) : $remarks->count();?>
@foreach($remarks as $remark)
    <?php
    if($remark->posted_by=='admin') {
        //$c = User::find($remark->admin_id);
        $c = $remark->admin;
    }
    else $c = User::find($remark->user_id);
    ?>

    <a name="remark-{{$remark->id}}"></a>
    @if( (admin() && $remark->posted_by!='admin' ) || (!admin() && $remark->posted_by=='admin') )
    <div class="coach-col remark-box remark-box-{{$remark->id}}">
        <div class="left-col">
    @else
    <div class="student-col remark-box remark-box-{{$remark->id}}">
            <div class="left-col">
    @endif
            @if($c->avatar=='')
                <img class="img-responsive img-col" src="http://placehold.it/156x156&text={{$c->username}}" />
            @else
                <img class="img-responsive img-col" src="{{url('assets/img/avatars/'.$c->avatar)}}" />
            @endif
            <div class="clearfix"></div>
            <h3>{{$c->first_name.' '.$c->last_name}}</h3>
            <h4>
                @if($remark->posted_by=='admin') 
                    Coach
                @else
                    Student
                @endif
            </h4>
        </div><!--left-col ends-->
        @if( (admin() && $remark->posted_by!='admin' ) || (!admin() && $remark->posted_by=='admin') )
        <div class="coach-textarea">
        @else
        <div class="student-textarea">
        @endif
            <span class="time pull-left do-tooltip" title="{{format_date($remark->created_at)}}">{{$remark->created_at->diffForHumans()}}</span>
            <div class="clearfix"></div>
            <span class='text'>{{$remark->content}}</span>
            
            <div class="attachment">
                @if($remark->attachments->count() > 0)
                    <h5>ATTACHMENTS</h5>
                    @foreach($remark->attachments as $a)
                        <p><span><a target='_blank' href="{{url("assets/uploads/attachments/$a->filename")}}">{{$a->orig_name}}</a></span> {{human_filesize(filesize("assets/uploads/attachments/$a->filename"))}}</p>
                    @endforeach
                 @endif
            </div>
           
            <ul class="list-unstyled option-box">
                @if(!admin())
                    @if($remark->read==0)
                    <li>
                        <a id="mark-remark-read-{{$remark->id}}" title="Mark As Read" class="do-tooltip"
                           onclick="mark_remark_read({{$remark->id}})"><i class="glyphicon glyphicon-ok"></i></a>  
                    </li>
                    @endif
                @else
                    @if($remark->attended==0 && $remark->admin_reply==0)
                    <li>
                         <a id="mark-remark-read-{{$remark->id}}" title="Mark As Attended" class="do-tooltip"
                                 onclick="mark_remark_attended({{$remark->id}})"><i class="glyphicon glyphicon-ok"></i></a>
                    </li>
                    @endif
                @endif

            @if( $i==$total && ((admin() && $remark->posted_by=='admin' ) || (!admin() && $remark->posted_by!='admin')) )
            
                    <li><a href="#" data-toggle="tooltip" title="" data-original-title="Edit" data-id='{{$remark->id}}'
                           class="do-tooltip link" onclick="edit_remark(event);"></a></li>
           @endif
            </ul>            
        </div><!--coach-textarea ends-->
        <div class="clearfix"></div>
    </div>
<?php ++$i;?>
@endforeach