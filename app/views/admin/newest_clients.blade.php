<table class='table table-condensed table-bordered table-striped'>
    <tbody>
            @foreach($newest as $n)
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

               <td class="text-center vert-align">
                    <span data-toggle="tooltip" class='do_tooltip' data-original-title='{{format_date($n->start_date)}}'>
                       
                    </span>
                    
                    <span data-toggle="tooltip" class="do-tooltip" title='{{format_date($n->start_date)}}'>
                     {{\Carbon\Carbon::createFromTimeStamp( strtotime( $n->start_date ) )->diffForHumans() }}
                    </span>
                </td>
            </tr>
            @endforeach
    </tbody>
</table>
            {{$newest->appends(array('collection' => 'newest'))->links()}}