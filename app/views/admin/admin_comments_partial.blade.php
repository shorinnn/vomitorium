<table class='table table-striped table-bordered'>
            @foreach($comments as $c)
                <tr><td>{{format_date($c->updated_at)}}, {{$c->updated_at->diffForHumans()}}</td>
                    <td>
                        <a title="Go to {{$c->user->username}}'s page" href='{{url('userpage/'.$c->user->id)}}'>{{$c->user->username}}</a></td>
                    <td>
                        <a title='View Comment' href='{{url('lesson/'.$c->block_answer->block->lesson->slug.'/'.$c->user->id.'#comment-'.$c->id)}}'>{{ $c->block_answer->block->lesson->title }} - 
                            {{ $c->block_answer->block->title }}</a></td>
                    <td>{{ Str::words($c->reply, 15) }}</td>
                </tr>
            @endforeach
             </table>
            {{$comments->links()}}