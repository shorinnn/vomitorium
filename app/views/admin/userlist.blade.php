<table class='table table-condensed table-bordered table-striped'>
    <tbody>
            @foreach($unattended as $u)
            <tr><td class="vert-align">
                @if($u->avatar!='')
                   <img width="32" alt="{{$u->username}}" title="{{$u->username}}" class="discussion-thumb" src="{{url('assets/img/avatars/'.$u->avatar)}}" />
               @else
                   <img width="32" alt="{{$u->username}}" title="{{$u->username}}" class="discussion-thumb" src="http://placehold.it/80x80&text={{$u->username}}" />
               @endif
               <a href='{{url('userpage/'.$u->id)}}' style="display: inline;">{{$u->username}}</a>               
                </td>
                <td class="text-center vert-align">
                <?php
                    $items = UserManager::unattended_answers($u->id) + UserManager::unattended_comments($u->id)  + UserManager::unattended_remarks($u->id);
                ?>
                    @if($items>0)
                        <span data-toggle="tooltip" class="badge do-tooltip alert-danger" title='{{$items}} Not Yet Reviewed {{ singplural($items,'Item')}}'>{{$items}}</span>
                    @endif
                                    </td>
                <td class="text-center vert-align">
                    <span data-toggle="tooltip" class="do-tooltip" title='{{format_date($u->last_update())}}'>
                     {{\Carbon\Carbon::createFromTimeStamp( strtotime( $u->last_update() ) )->diffForHumans() }}
                    </span>
                </td>
                <td class="text-center vert-align">
                    @if($u->answers->count()>0)
                    <span data-toggle="tooltip" class="do-tooltip" title='{{format_date($u->answers()->orderBy('updated_at','DESC')->first()->updated_at)}}'>
                             Last Update: {{\Carbon\Carbon::createFromTimeStamp( strtotime( $u->answers()->orderBy('updated_at','DESC')->first()->updated_at ) )->diffForHumans() }}
                    </span>
                    @endif
                </td>
            </tr>
            @endforeach
    </tbody>
</table>
            {{$unattended->appends(array('collection' => 'unattended'))->links()}}