@extends('layouts.saas')

@section('content')
   
<h1>Lesson Notifications</h1>

    @if(count($accounts['date']==0))
    <div class='alert alert-warning'>
        No lesson notifications for {{date("M d Y")}}
    </div>
    @endif
    @foreach($accounts['date'] as $a=>$val)
    <div class='notification-accounts'>Account {{$a}}.imacoa.ch notified users:</div>
        @foreach($val as $l=>$users)
            <div class='notification-lessons'>Lesson {{$l}}</div>
            @foreach($users as $u)
                <div class='notification-users'>{{"$u->first_name $u->last_name ($u->email | ID: $u->id)"}}</div>
            @endforeach
        @endforeach
    @endforeach
    
    @if(count($accounts['days']==0))
    <div class='alert alert-warning'>
        No "After X Days" lesson notifications
    </div>
    @endif
    @foreach($accounts['days'] as $a=>$val)
    <div class='notification-accounts'>Account {{$a}}.imacoa.ch notified users:</div>
        @foreach($val as $l=>$users)
            <div class='notification-lessons'>Lesson {{$l}}</div>
            @foreach($users as $u)
                <div class='notification-users'>{{"$u->first_name $u->last_name ($u->email | ID: $u->id)"}}</div>
            @endforeach
        @endforeach
    @endforeach

@stop