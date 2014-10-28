{{View::make('admin.dash.header')->withCurrent_program($current_program)}}

{{View::make('admin.dash.buttons')}}

<div class='col-lg-8'>
    @if($current_program->users()->count()>0)
        <h4 class="green-bg">New Submissions (Unattended {{client_term()}})</h4>
        <div class='dash-content'>
            {{View::make('admin.userlist')->withUnattended($unattended)}}
        </div>

        <h4 class="green-bg">New Private Messages</h4>
        <div class='dash-content'>
            {{View::make('admin.private_messages')->withPm($pm)}}
        </div>
    @else
        {{View::make('admin.dash.add_clients')}}
    @endif
</div>
