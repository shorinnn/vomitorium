{{View::make('admin.dash.header')->withCurrent_program($current_program)}}

{{View::make('admin.dash.buttons')}}

<div class='col-lg-8'>
    @if($current_program->users()->count()>0)
        <h4 class="green-bg">Newest  {{client_term()}}</h4>
        <div class='dash-content'>
            {{View::make('admin.newest_clients')->withNewest($newest)}}
        </div>

        <h4 class="green-bg">Not Yet Reviewed {{client_term()}}</h4>
        <div class='dash-content'>
            {{View::make('admin.userlist')->withUnattended($unattended)}}
        </div>

        <h4 class="green-bg">New Private Messages</h4>
        <div class='dash-content'>
            {{View::make('admin.private_messages')->withPm($pm)}}
        </div>

        <h4 class="green-bg">New Submissions</h4>
        <div class='dash-content'>       
            {{View::make('admin.admin_submissions_partial')->withSubmissions($new_submissions)}}
        </div>
    @else
        {{View::make('admin.dash.add_clients')}}
    @endif
</div>

