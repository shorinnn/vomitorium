<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{{$meta['pageDescription'] or ''}}}">
    <meta name="keywords" content="{{{$meta['pageKeywords'] or ''}}}">
    <title>{{{ isset($meta['pageTitle']) ? $meta['pageTitle'].' - ' : '' }}}BrilliantU.com</title>
    {{HTML::style('../assets/font-awesome/css/font-awesome.min.css')}}
    {{HTML::style('../assets/builds/combined.min.css')}}
    {{HTML::style('../assets/css/custom.css')}}
    <!--[if lt IE 8]>
        {{HTML::style('../assets/css/bootstrap-ie7.css')}}
    <![endif]-->
</head>

<body>
    <div class="navbar navbar-inverse " role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- You'll want to use a responsive image option so this logo looks good on devices - I recommend using something like retina.js (do a quick Google search for it and you'll find it) -->
                <a class="navbar-brand" href="{{{action('PagesController@index')}}}">BrilliantU</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav navbar-right">
                    @if(admin())
                    <li>{{link_to('skills','Skill')}}</li>
                    <li>{{link_to('lessons','Lesson Manager')}}</li>
                    <li>{{link_to('chapters','Chapter')}}</li>
                    <li>{{link_to('users','Users')}}</li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">About Us <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>{{link_to('about-us/video-introduction','Video Introduction')}}</li>
                            <li>{{link_to('who-we-are/success-stories','Success Stories')}}</li>
                            <li>{{link_to('who-we-are/anthony-di-marco','Anthony Di marco')}}</li>
                            <li>{{link_to('who-we-are/michael-reddy','Michael Reddy')}}</li>
                        </ul>
                    </li>
                    
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Courses <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                                    @if(Auth::guest()==='This is supposed to fail!')          
                                     {{--   <li>{{link_to('the-brilliant-career','The Brilliant Career')}}</li>
                                        <li>{{link_to('the-brilliant-career/course-overview','Course Overview')}}</li>
                                        <li>{{link_to('the-brilliant-career/our-programs','Our Programs')}}</li>
                                        <li>{{link_to('login','Member Login')}}</li>     --}}        
                                    @else
                                            <?php
                                           $chapter = -1;
                                           $lessons = Lesson::where('published','1')->orderBy('chapter_ord','asc')->orderBy('ord','asc')->get();
                                           foreach($lessons as $lesson){
                                               if(Auth::guest()) $user_id = 0;
                                               else{
                                                   $user_id = Session::has('user_id') ? Session::get('user_id') : Auth::user()->id;
                                               }
                                               
                                            if($chapter!=$lesson->chapter_id){
                                                $last_ord = DB::table('lessons')->where('chapter_id', $lesson->chapter_id)->max('ord');
                                                $chapter = $lesson->chapter_id;
                                                ?>
                                    <li><a href="#">{{$lesson->chapter->title or ''}}</a></li>
                                                <?php } ?>
                                                <li>
                                                    @if(Session::has('user_id'))
                                                        <a href="{{url("lesson/$lesson->slug/".Session::get('user_id'))}}">&raquo; 
                                                    @else
                                                        <a href="{{url("lesson/$lesson->slug")}}">&raquo; 
                                                    @endif
                                                        
                                                        {{$lesson->title}}
                                                     @if(admin())
                                                        <span class="badge" title='{{UserManager::unattended_answers($user_id, $lesson->id)}} Unattended {{ 
                                                            singplural(UserManager::unattended_answers($user_id, $lesson->id),'Submissions')}}'>
                                                            {{UserManager::unattended_answers($user_id, $lesson->id)}}</span>
                                                        <span class="badge" title='{{UserManager::unattended_comments($user_id, $lesson->id)}} Unattended {{
                                                            singplural(UserManager::unattended_comments($user_id, $lesson->id),'Comments')}}'>
                                                            {{UserManager::unattended_comments($user_id, $lesson->id)}}</span>
                                                     @endif
                                                    </a></li>
                                                <?php
                                           }

                                        ?>
                                    @endif
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Resources <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>{{link_to('resources/help-documents','Help Documents')}}</li>
                            <li>{{link_to('resources/webinars','Webinars')}}</li>
                        </ul>
                    </li>
                     <li>{{link_to('blog','Blog')}}</li>
                     <li>{{link_to('contact-us','Contact Us')}}</li>
                    @if (Auth::guest())
                     <li>{{link_to('login','Login')}}</li>
                     <li>{{link_to('register','Register')}}</li>
                    @else
                     <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @if(Auth::user()->avatar!='')
                            <img height="24" src="{{url('assets/img/avatars/'.Auth::user()->avatar)}}" />
                            @else
                            <img height="24" src="http://placehold.it/80x80&text={{Auth::user()->username}}" />
                            @endif
                            {{Auth::user()->username}} 
                            <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>{{link_to('settings','Settings')}}</li>
                            <li>{{link_to('logout','Log out')}}</li>
                        </ul>
                    </li>
                    
                    @endif
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </div>

     @yield('content')
    <!-- JavaScript -->
    <!--[if lt IE 9]>
    {{HTML::script('../assets/js/html5shiv.min.js')}}
    {{HTML::script('../assets/js/respond.min.js')}}
    <![endif]-->

    
    <script src="{{url('jsconfig')}}"></script>
    {{HTML::script('../assets/builds/combined.min.js')}}
    {{HTML::script('../assets/js/custom.js')}}
    @if(isset($meta)) 
        {{extra_javascripts($meta)}}
    @endif
    
    <a data-toggle='tooltip' data-placement='right' title='Change Design' class='btn btn-danger do-tooltip' style='position:fixed; top:0px; left:0px;' href="{{url('change_style')}}"><i class='glyphicon glyphicon-refresh'></i></a>
</body>

</html>
