
<!doctype html>
<!--[if IE 7 ]> <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]--><head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="description" content="{{{$meta['pageDescription'] or ''}}}">
        <meta name="keywords" content="{{{$meta['pageKeywords'] or ''}}}">
        <title>{{{ isset($meta['pageTitle']) ? $meta['pageTitle'].' - ' : '' }}}{{sys_settings('domain')}}</title>
        <!--{{HTML::style('assets/builds/combined.min.css')}}-->
                    {{HTML::style('assets/css/jqueryui/smoothness/jquery-ui-1.10.4.custom.min.css')}}
                    {{HTML::style('assets/css/editable/css/bootstrap-editable.css')}}
                    {{HTML::style('assets/css/summernote.css')}}
                    {{HTML::style('assets/css/summernote-bs3.css')}}        
                     <!--{{HTML::style('assets/css/custom.css')}}-->
    <!--Font awesome CSS-->
        {{HTML::style('assets/font-awesome/css/font-awesome.min.css')}}
	<!-- Latest compiled and minified CSS -->
        <!--{{HTML::style('assets/css/bootstrap/css/bootstrap.css')}}-->
        {{HTML::style('assets/css/bootstrap/bootstrap.min.css')}}
        {{HTML::style('assets/css/bootstrap/css/bootstrap-responsive.css')}}
	<!--Style css-->
	{{HTML::style('assets/css/style.css')}}
    <!--Media query css-->
    {{HTML::style('assets/css/media-query.css')}}
    
    <!--{{HTML::style('assets/css/flexslider.css')}}-->
   
    <!--{{HTML::style('assets/css/lesson.css')}}-->
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <?php
    /*
	<header>
    	<div class="container">
            <span class="white-bg">
                
            </span>
        	<div class="logo-bg">
                    
        		<h1 class="logo">
                            <!--<a href="#"><img src="assets/img/logo.png" alt=""></a>-->
                            <a href="{{url('/')}}" class="main-logo">
                                @if(sys_settings('logo')=='')
                                <img src="{{url('assets/img/layout/logos/top-logo.png')}}" alt="">
                                @else
                                <img src="{{url('assets/img/logos/'.sys_settings('logo'))}}" alt="">
                                @endif
                            </a>
                        </h1>
            </div><!--logo-bg ends-->
            <div class="memember-col">
            	
                    @if (Auth::guest())
                    <ul class="list-unstyled">
                        @if(Request::url() == url("login"))
                            <li class="active"><a href="{{url('login')}}" class="login">Login</a></li>
                        @else 
                            <li><a href="{{url('login')}}" class="login">Login</a></li>
                        @endif

                        @if(Request::url() == url("register"))
                            <li class="active"><a href="{{url('register')}}" class="register">Register</a></li>
                        @else
                            <li><a href="{{url('register')}}" class="register">Register</a></li>
                        @endif
                    @else
                    <ul class="nav nab-bar">
                         @if(Request::url() == url("settings") || current_controller()=='Admin' )
                         <li class="dropdown active">
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
                <div class="clearfix"></div>
            </div><!--memember-col ends-->
            <div class="navbar navbar-default">
      			<div class="navbar-header">
        			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      			</div><!--navbar-header ends-->
      			<div class="navbar-collapse collapse">
        			<ul class="nav navbar-nav main-nav-items">
                                    @if(Auth::guest())
                                            @if(Request::url() == url(""))
                                                <li class="active"><a href="{{url('/')}}">Home</a></li>
                                            @else
                                                <li><a href="{{url('/')}}">Home</a></li>
                                            @endif

                                            <li><a href="#">Courses</a></li>

                                            @if(Request::url() == url("contact-us"))
                                                <li class="active"><a href="{{url('contact-us')}}">Support</a></li>
                                            @else
                                                <li><a href="{{url('contact-us')}}">Support</a></li>
                                            @endif
                                    @else
                                        @if(admin())
                                            {{View::make('layouts.admin_menu')}}
                                        @else
                                        @endif
                                    @endif
        			</ul>
      			</div><!--nav-collapse ends--> 
    		</div><!--navbar ends-->
        </div><!--container ends-->
    </header><!--header ends-->
    */
    ?>
    <header>
    	<div class="container">
        	<span class="white-bg"></span>
        	<div class="logo-bg">
        		<h1 class="logo"><a href="#"><img src="assets/img/logo.png" alt=""></a></h1>
            </div><!--logo-bg ends-->
            <div class="memember-col">
            	<ul class="list-unstyled">
            		<li><a href="#" class="login">Login</a></li>
                    <li><a href="#" class="register">Register</a></li>
                    <li><a href="#" class="search">Search</a></li>
            	</ul>
                <div class="clearfix"></div>
            </div><!--memember-col ends-->
            <div class="navbar navbar-default">
      			<div class="navbar-header">
        			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      			</div><!--navbar-header ends-->
      			<div class="navbar-collapse collapse">
        			<ul class="nav navbar-nav">
          				<li><a href="#">Home</a></li>
          					<li class="active"><a href="#">Courses</a></li>
          					<li><a href="#">About Us</a></li>
          				<li><a href="#">Support</a></li>
        			</ul>
      			</div><!--nav-collapse ends--> 
    		</div><!--navbar ends-->
        </div><!--container ends-->
    </header><!--header ends-->
    
    <div class="clearfix"></div>
    @if(isset($meta['header_img_text']))
                <section class="banner">
                    <div class="container">
                       <!--	<img src="images/banner/top-banner.jpg" alt="" class="img-responsive" />-->
                        <h2>{{$meta['header_img_text'] or sys_settings('domain')}}</h2>
                    </div>
                </section>
            @endif
            
    <div class="clearfix"></div>
    
    <section class="content">
    	<div class="container">
        	<div class="main-content">
            	@yield('content')
                <div class="clearfix"></div>
            </div><!--main-content ends-->
        </div><!--container ends-->
    </section><!--content ends-->
    
	<!--<a class="go-to-top hidden-xs" href="#"></a>-->
    
    <script src="{{url('jsconfig')}}"></script>
    {{HTML::script('assets/js/modernizr.custom.js')}}
    {{HTML::script('../assets/builds/combined.min.js')}}
    {{HTML::script('../assets/js/elastic/elastic.js')}}
    {{HTML::script('assets/js/main.js')}}
    {{HTML::script('../assets/js/custom.js')}}
     @if(isset($meta)) 
        {{extra_javascripts($meta)}}
    @endif
    
    <!--<a data-toggle='tooltip' data-placement='right' title='Change Design' class='btn btn-danger do-tooltip' style='position:fixed; top:0px; left:0px;' href="{{url('change_style')}}"><i class='glyphicon glyphicon-refresh'></i></a>-->
</body>
</html>
