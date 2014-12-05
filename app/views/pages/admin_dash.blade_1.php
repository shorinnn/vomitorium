@extends($layout)

@section('content')
<div class="section">


        <div class="container">
            <h1>Welcome {{Auth::user()->username}}</h1>
            <h3>New Submissions</h3>
            <table class='table table-striped table-bordered'>
            @foreach($new_submissions as $s)
                <tr><td>{{format_date($s->updated_at)}}, {{$s->updated_at->diffForHumans()}}</td>
                    <td>
                        <a title="Go to {{$s->user->username}}'s page" href='{{url('userpage/'.$s->user->id)}}'>{{$s->user->username}}</a></td>
                    <td>
                        <a title = 'View Submission' href='{{url('lesson/'.$s->block->lesson->slug.'/'.$s->user->id.'#block-'.$s->block->id)}}'>{{ $s->block->lesson->title }} - 
                            {{ $s->block->title }}</a></td>
                </tr>
            @endforeach
            </table>
            <a href="{{url('new_submissions')}}">View All</a>
            <h3>Not Yet Reviewed Submissions</h3>
             <table class='table table-striped table-bordered'>
            @foreach($unattended_submissions as $s)
                <tr><td>{{format_date($s->updated_at)}}, {{$s->updated_at->diffForHumans()}}</td>
                    <td>
                        <a title="Go to {{$s->user->username}}'s page" href='{{url('userpage/'.$s->user->id)}}'>{{$s->user->username}}</a></td>
                    <td>
                        <a title='View Submissions' href='{{url('lesson/'.$s->block->lesson->slug.'/'.$s->user->id.'#block-'.$s->block->id)}}'>{{ $s->block->lesson->title }} - 
                            {{ $s->block->title }}</a></td>
                </tr>
            @endforeach
            </table>
            <a href="{{url('unattended_submissions')}}">View All</a>
            <h3>New Messages</h3>
             <table class='table table-striped table-bordered'>
            @foreach($new_comments as $c)
                <tr><td>{{format_date($c->updated_at)}}, {{$c->updated_at->diffForHumans()}}</td>
                    <td>
                        <a title="Go to {{$c->user->username}}'s page"  href='{{url('userpage/'.$c->user->id)}}'>{{$c->user->username}}</a></td>
                    <td>
                        <a title='View Comment' href='{{url('lesson/'.$c->block_answer->block->lesson->slug.'/'.$c->user->id.'#comment-'.$c->id)}}'>{{ $c->block_answer->block->lesson->title }} - 
                            {{ $c->block_answer->block->title }}</a></td>
                    <td>{{ Str::words($c->reply, 15) }}</td>
                </tr>
            @endforeach
             </table>
            <a href="{{url('new_messages')}}">View All</a>
            <h3>Not Yet Reviewed Messages</h3>
             <table class='table table-striped table-bordered'>
            @foreach($unattended_comments as $c)
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
            <a href="{{url('unattended_messages')}}">View All</a>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop