@extends($layout)

@section('content')
<form method="post" action="{{url('users/chat_permissions/'.$user->id)}}" class="update_form">
<div class="section">

        <div class="container">
            @if(count($user->programs())<1)
            <div class="alert alert-danger alert-warning">
                Cannot assign a coach because this user is not associated with any programs yet.
            </div>
            @else
            <table class="table table-striped table-bordered">
                @foreach($user->programs() as $p)
                <tr><td>{{$p->name}} <input type="hidden" name="program[]" value="{{$p->id}}"</td>
                    <td>
                        {{ Form::select( "chat_permissions[$p->id]",
                                       [ '1'=>'Allow Group Conversations', '0'=>"Don't Allow Group Conversations" ], 
                                       $user->chat_permission($p->id, 'group_conversations') ) }}
                        {{ Form::select( "coach_chat_permissions[$p->id]",
                                       [ '1'=>'Allow Coach Conversations', '0'=>"Don't Allow Coach Conversations" ], 
                                       $user->chat_permission($p->id, 'coach_conversations') ) }}
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
            <button class="btn btn-default">Update</button>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
</form>
@stop