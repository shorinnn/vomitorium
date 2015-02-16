<div id="ajax-content">
     <br />
     @if(isset($is_search))
        <br />
    @else
    {{$users->links()}}
    @endif
<div class="table-responsive" style="overflow-x:auto;">
    <table class="table table-bordered table-striped">
    <thead>
        <tr><th>User</th><th>First Name</th><th>Last Name</th><th>Email</th>
            <!--<th>Created at</th>-->
            <th>Coaching | Group Conv.</th>
            <th>Payment Plans</th></tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr class="list-row list-row-{{$user->id}}">
        <td>
            <a class="editable" href="#" id="username" data-type="text" data-pk="{{$user->id}}" 
               data-name="username" data-url="{{url('users/update')}}" data-original-title="Enter username" data-mode='inline'>{{$user->username}}
            </a>
            <br />
            <br />
            <a data-toggle="tooltip" title="Assign Coach" href='{{url('users/assign_coach/'.$user->id)}}'
               class='do-tooltip btn btn-primary btn-sm' ><i class="glyphicon glyphicon-user"></i></a>
            <button data-toggle="tooltip" title="Change password" type="button" autocomplete="off" class='do-tooltip btn btn-primary btn-sm'
                    onclick='change_password("{{URL("users/$user->id/change_password")}}","{{$user->id}}")'><i class="glyphicon glyphicon-lock"></i></button>
           
            <a data-toggle="tooltip" title="View user page" type="button" href='{{url('userpage/'.$user->id)}}' class='do-tooltip btn btn-primary btn-sm' 
               onclick='show_busy()'><i class="glyphicon glyphicon-search"></i></a>
            <br />
            <br />
            <a data-toggle="tooltip" title="Chat Permissions" href='{{url('users/chat_permissions/'.$user->id)}}'
               class='do-tooltip btn btn-primary btn-sm' ><i class="glyphicon glyphicon-volume-up"></i></a>
            <button data-toggle='tooltip' title='Delete user' type="button" autocomplete="off" class='do-tooltip btn btn-danger btn-sm' onclick='del({{$user->id}},"{{url("users/$user->id")}}")'><i class='glyphicon glyphicon-trash'></i></button>
            
            
        </td>
       
        
        <td>
            <a class="editable" href="#" id="first_name" data-type="text" data-pk="{{$user->id}}" 
               data-name="first_name" data-url="{{url('users/update')}}" data-original-title="Enter First Name" data-mode='inline'>{{$user->first_name}}</a>
        </td>
        <td>
            <a class="editable" href="#" id="last_name" data-type="text" data-pk="{{$user->id}}" 
               data-name="last_name" data-url="{{url('users/update')}}" data-original-title="Enter Last Name" data-mode='inline'>{{$user->last_name}}
            </a>
        </td>
        <td>
            <a class="editable" href="#" id="email" data-type="text" data-pk="{{$user->id}}" 
               data-name="email" data-url="{{url('users/update')}}" data-original-title="Enter email" data-mode='inline'>{{$user->email}}
            </a>
        </td>
        <td>
            @if($user->chat_permission(Session::get('program_id'), 'coach_conversations')==1)
                <i class="glyphicon glyphicon-ok do-tooltip" title="Has Coach Conversations Permission"></i>
            @else
                <i class="glyphicon glyphicon-remove do-tooltip" title="Doesn't Have Coach Conversations Permission"></i>
            @endif
            
            | 
            
            @if($user->chat_permission(Session::get('program_id'), 'group_conversations')==1)
                <i class="glyphicon glyphicon-ok do-tooltip" title="Has Group Conversations Permission"></i>
            @else
                <i class="glyphicon glyphicon-remove do-tooltip" title="Doesn't have Group Conversations Permission"></i>
            @endif
        </td>
        <td>
            <?php
                $plans = DB::table('programs_users')->where('user_id', $user->id)->get();
                foreach($plans as $p){
                    $plan = PaymentPlan::find($p->subscription_id);
                    if($p->expires=='') $status='Active';
                    else{
                        if(time() < strtotime($p->expires) ) $status = 'Active';
                        else $status = 'Expired';
                    }
                    if($plan!=null){
                        echo "$plan->name ($status)<br />";
                    }
                }
            ?>
        </td>
        <!--
        <td>{{$user->created_at}}</td>
        -->

 </tr>
        @endforeach
    </tbody>
</table>
</div>
    @if(isset($is_search))
        <br />
    @else
    {{$users->links()}}
    @endif
</div>