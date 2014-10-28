<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="description" content="{{{$meta['pageDescription'] or ''}}}">
        <meta name="keywords" content="{{{$meta['pageKeywords'] or ''}}}">
        <title>{{{ isset($meta['pageTitle']) ? $meta['pageTitle'].' - ' : '' }}}{{sys_settings('domain')}}</title>
        <meta name="author" content="">
        {{HTML::style('assets/font-awesome/css/font-awesome.min.css')}}
        {{HTML::style('assets/css/bootstrap/css/bootstrap.css')}}
        {{HTML::style('assets/css/bootstrap/css/bootstrap-responsive.css')}}
        {{HTML::style('assets/css/flexslider.css')}}
        {{HTML::style('assets/builds/combined.min.css')}}
        <!--<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>-->
        {{HTML::style('assets/css/style-backup.css')}}
        {{HTML::style('assets/css/media-query.css')}}
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800' rel='stylesheet' type='text/css'>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{HTML::style('assets/css/custom.css')}}
        {{HTML::style('assets/css/lesson.css')}}
        
    </head>

<body>

	<body>

	<header class="container">
    	<div class="row">
        	<div class="col-md-12">
            	<a href="{{url('/')}}" class="main-logo">
                    @if(sys_settings('logo')=='')
                    <img src="{{url('assets/img/layout/logos/top-logo.png')}}" alt="">
                    @else
                    <img src="{{url('assets/img/logos/'.sys_settings('logo'))}}" alt="">
                    @endif
                </a>
                    <div class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> 
                                <span class="icon-bar"></span> 
                                <span class="icon-bar"></span> 
                                <span class="icon-bar"></span> 
                            </button>
                        </div>                             
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right main-nav-items">
                            @if(admin())
                                <?php
                                    $programs = get_programs();
                                ?>
                                @if($programs!=null && $programs->count() > 0)
                                    <li class='program_chooser'>
                                       Current Program:<br />
                                       <select class="form-control" id='program_chooser' onchange='choose_program()'>
                                           <option value='0'>Programs Picker</option>
                                           @foreach($programs as $p)
                                                <?php
                                                    $selected = '';
                                                    if(Session::has('program_id') && Session::get('program_id')==$p->id) $selected='selected="selected"';
                                                ?>
                                               <option title="{{$p->name}}" value='{{$p->id}}' {{$selected}}>{{Str::limit($p->name, 20)}}</option>
                                           @endforeach
                                       </select>
                                    </li>
                                @endif
                            @if(Request::url() == url(""))
                            <li class="current_lesson">{{link_to('/','Dashboard')}}</li>
                            @else
                             <li>{{link_to('/','Dashboard')}}</li>
                            @endif
                            @if(current_controller()=='Skills' || current_controller()=='Programs' ||
                            current_controller()=='Lessons'
                            || current_controller()=='Chapters'|| current_controller()=='Categories')
                                <li class="dropdown current_lesson">
                            @else
                                <li class="dropdown">
                            @endif
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Program Tools <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>{{link_to('programs','Programs')}}</li>
                                    <li>{{link_to('lessons','Course Editor')}}</li>                                   
                                    <li>{{link_to('chapters','Chapter Editor')}}</li>
                                    <li>{{link_to('categories','Block Category Manager')}}</li>                                    
                                    <li>{{link_to('skills','Skill-block Options')}}</li>
                                    <li>{{link_to('reports','Reports')}}</li>
                                   <!-- <li>{{link_to('system_settings','System Settings')}}</li> current_controller()=='Admin' || -->
                                    <!--<li>{{link_to('users','Users')}}  || current_controller()=='UserManager' </li>-->
                                </ul>
                            @else
                               <li class='program_chooser'>
                                   @if(!Auth::guest())
                                       Current Program:<br />
                                       <select class="form-control" id='program_chooser' onchange='choose_program()'>
                                           @foreach(Auth::user()->programs() as $p)
                                                <?php
                                                    $selected = '';
                                                    if(Session::has('program_id') && Session::get('program_id')==$p->id) $selected='selected="selected"';
                                                ?>
                                               <option title="{{$p->name}}" value='{{$p->id}}' {{$selected}}>{{Str::limit($p->name, 20)}}</option>
                                           @endforeach
                                       </select>
                                       @endif
                                    </li>
                            @endif
                            
                            
                                @if(!admin())
                                @if(current_controller()=='Course')
                                    <li class=" current_lesson">
                                @else
                                    <li>
                                @endif
                                    <a href="{{URL('courses')}}">Courses</a>
                                @else
                                @if(current_controller()=='UserManager')
                                    <li class=" current_lesson">
                                @else
                                    <li>
                                @endif
                                {{link_to('users','Clients')}}
                                <!--
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Courses <b class="caret"></b></a>
                        <ul class="dropdown-menu">
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
                                                        @if(Request::url() == url("lesson/$lesson->slug/".Session::get('user_id')))
                                                            <a class='current_lesson' href="{{url("lesson/$lesson->slug/".Session::get('user_id'))}}">&raquo; 
                                                        @else
                                                            <a href="{{url("lesson/$lesson->slug/".Session::get('user_id'))}}">&raquo; 
                                                        @endif
                                                        
                                                    @else
                                                        @if(Request::url() == url("lesson/$lesson->slug"))
                                                            <a class='current_lesson' href="{{url("lesson/$lesson->slug")}}">&raquo; 
                                                        @else
                                                            <a href="{{url("lesson/$lesson->slug")}}">&raquo; 
                                                        @endif
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
                        </ul>-->
                    </li>
                            @endif
                             @if(Request::url() == url("contact-us"))
                                 <li class="current_lesson">{{link_to('contact-us','Support')}}</li>
                             @else
                                 <li>{{link_to('contact-us','Support')}}</li>
                             @endif
                            
                    @if (Auth::guest())
                        @if(Request::url() == url("login"))
                             <li class='current_lesson'>{{link_to('login','Login')}}</li>
                         @else
                             <li>{{link_to('login','Login')}}</li>
                         @endif
                         @if(Request::url() == url("register"))
                             <li class='current_lesson'>{{link_to('register','Register')}}</li>
                         @else
                             <li>{{link_to('register','Register')}}</li>
                         @endif
                    @else
                        @if(Request::url() == url("settings") || current_controller()=='Admin' )
                         <li class="dropdown current_lesson">
                         @else
                         <li class="dropdown">
                         @endif
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                @if(Auth::user()->avatar!='')
                                <img height="24" src="{{url('assets/img/avatars/'.Auth::user()->avatar)}}" />
                                @else
                                <img height="24" src="http://placehold.it/80x80&text={{Auth::user()->username}}" />
                                @endif
                                {{Auth::user()->username}} 
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li>{{link_to('settings','Profile')}}</li>
                                @if(admin())
                                <li>{{link_to('system_settings','System Settings')}}</li>
                                @endif
                                <li>{{link_to('logout','Log out')}}</li>
                            </ul>
                        </li>
                    @endif
                        </ul>
                    </div>                        
            </div>
        </div>
    </header>
            @if(isset($meta['header_img_text']))
                <section class="container-fluid top-banner-image">
                    <br />  
                    <div class="col-md-12" <?php
                 if(sys_settings('bgimage')!=''){
                   echo "style= \"background-image: url('".url('assets/img/backgrounds/'.sys_settings('bgimage'))."') !important;\"";
                 }
                ?>>
                       <!--	<img src="images/banner/top-banner.jpg" alt="" class="img-responsive" />-->
                        <p class="text-center" style="<?php
                           if(sys_settings('tagline_foreground_color')!=''){
                               echo "color: #".sys_settings('tagline_foreground_color')." !important;";
                           }
                           if(sys_settings('tagline_background_color')!=''){
                               echo "background-color: #".sys_settings('tagline_background_color')." !important;";
                           }   
                           ?>">{{$meta['header_img_text'] or 'Your Achievements'}}</p>
                    </div>
                </section>
            @endif
    @yield('content')
    	<!--Scripts-->
        <script src="{{url('jsconfig')}}"></script>
    {{HTML::script('assets/js/modernizr.custom.js')}}
    {{HTML::script('../assets/builds/combined.min.js')}}
    {{HTML::script('../assets/js/elastic/elastic.js')}}
    {{HTML::script('../assets/js/custom.js')}}
     @if(isset($meta)) 
        {{extra_javascripts($meta)}}
    @endif
    
    <a data-toggle='tooltip' data-placement='right' title='Change Design' class='btn btn-danger do-tooltip' style='position:fixed; top:0px; left:0px;' href="{{url('change_style')}}"><i class='glyphicon glyphicon-refresh'></i></a>
</body>
</html>
