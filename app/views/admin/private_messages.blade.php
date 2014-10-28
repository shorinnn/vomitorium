<table class='table table-condensed table-bordered table-striped'>
    <tbody>
            @foreach($pm as $n)
            <?php $u = User::find($n->user_id);?>
            <tr><td class="vert-align">
                @if($u->avatar!='')
                   <img width="32" alt="{{$u->username}}" title="{{$u->username}}" 
                        class="discussion-thumb" src="{{url('assets/img/avatars/'.$u->avatar)}}" />
               @else
                   <img width="32" alt="{{$u->username}}" title="{{$u->username}}" 
                        class="discussion-thumb" src="http://placehold.it/80x80&text={{$u->username}}" />
               @endif
               <a href='{{url('userpage/'.$u->id)}}' style="display: inline;">{{$u->username}}</a>               
                </td>
                <td>
                   {{$n->content}} 
                   <a href='{{url('inbox/'.$n->id)}}'>Read message</a>
                </td>
               <td class="text-center vert-align">
                    <span data-toggle="tooltip" class='do_tooltip' data-original-title='{{format_date($n->created_at)}}'>
                       
                    </span>
                    
                    <span data-toggle="tooltip" class="do-tooltip" title='{{format_date($n->created_at)}}'>
                     {{\Carbon\Carbon::createFromTimeStamp( strtotime( $n->created_at ) )->diffForHumans() }}
                    </span>
                </td>
            </tr>
            @endforeach
    </tbody>
</table>
            {{$pm->appends(array('collection' => 'private_messages'))->links()}}