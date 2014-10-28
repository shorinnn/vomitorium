@if(Session::has('program_id'))
        @foreach(Program::find(Session::get('program_id'))->active_announcements() as $a)
        <section class="banner announcement-msg">
            <p><i class='glyphicon glyphicon-bullhorn'></i> 
                {{$a->content}}</p>
        </section>
        @endforeach
    @endif