@extends($layout)

@section('content')
<div class="section">


        <div class="container">
           @if($user->avatar!='')
               <img alt="{{$user->username}}" title="{{$user->username}}" class="pull-left discussion-thumb" src="{{url('assets/img/avatars/'.$user->avatar)}}" />
           @else
               <img alt="{{$user->username}}" title="{{$user->username}}" class="pull-left discussion-thumb" src="http://placehold.it/80x80&text={{$user->username}}" />
           @endif
           <br /> Email: {{$user->email}}
           <br /> Registered: {{format_date($user->created_at)}}, {{$user->created_at->diffForHumans()}}
           <br />
           <br />
           @if( admin() )
           <a class='btn btn-primary' href='{{ action('AdminController@loginAs', $user->id) }}'>Login To {{$user->username}}'s account</a>
           @endif
           <h2>Courses</h2>
           <?php $chapter = -1 ;?>
           @foreach($lessons as $l)
           <?php
            if($chapter!= $l->chapter_id && $l->chapter_id > 0){
                $chapter = $l->chapter_id;
                echo '<h4>'.$l->chapter->title.'</h4>';
            }
            $items = UserManager::unattended_answers($user->id, $l->id) + UserManager::unattended_comments($user->id, $l->id) + UserManager::unattended_remarks($user->id, $l->id);
           ?>
               @if($items>0)
                   <span data-toggle="tooltip" class="badge do-tooltip alert-danger" title='{{$items}} Not Yet Reviewed {{ singplural($items,'Item')}}'>{{$items}}</span>
               @else
                   <span data-toggle="tooltip" class="badge do-tooltip invisible" title='{{$items}} Not Yet Reviewed {{ singplural($items,'Item')}}'>{{$items}}</span>
               @endif
                {{link_to("lesson/$l->slug/$user->id",$l->title)}}
           <br />
           @endforeach
       </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    
@stop