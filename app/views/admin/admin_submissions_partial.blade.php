<table class='table table-striped table-bordered'>
    @foreach($submissions as $s)
    <tr>
        <td class="vert-align">
            @if($s->user->avatar!='')
            <img width="32" alt="{{$s->user->username}}" title="{{$s->user->username}}" class="discussion-thumb" 
                 src="{{url('assets/img/avatars/'.$s->user->avatar)}}" />
            @else
            <img width="32" alt="{{$s->user->username}}" title="{{$s->user->username}}" class="discussion-thumb" 
                 src="http://placehold.it/80x80&text={{$s->user->username}}" />
            @endif
            <a href='{{url('userpage/'.$s->user->id)}}' style="display: inline;">{{$s->user->username}}</a>   
        </td>
        <td class="text-center vert-align">
            <a title='View Submission' href='{{url('lesson/'.$s->block->lesson->slug.'/'.$s->user->id.'#block-'.$s->block->id)}}'>{{ $s->block->lesson->title }} - 
            {{ $s->block->title }}</a>
        </td>
        <td class="text-center vert-align">
            <span data-toggle="tooltip" class='do_tooltip' data-original-title='{{format_date($s->updated_at)}}'>

            </span>

            <span data-toggle="tooltip" class="do-tooltip" title='{{format_date($s->updated_at)}}'>
             {{\Carbon\Carbon::createFromTimeStamp( strtotime( ($s->updated_at)) )->diffForHumans() }}
            </span>
        </td>
    </tr>
            
    @endforeach
</table>
 {{$submissions->appends(array('collection' => 'submissions'))->links()}}