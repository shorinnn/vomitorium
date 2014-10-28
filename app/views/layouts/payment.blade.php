
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
                     
    <!--Font awesome CSS-->
        {{HTML::style('assets/font-awesome/css/font-awesome.min.css')}}
	<!-- Latest compiled and minified CSS -->
        <!--{{HTML::style('assets/css/bootstrap/css/bootstrap.css')}}-->
        {{HTML::style('assets/css/bootstrap/bootstrap.min.css')}}
        {{HTML::style('assets/css/bootstrap/css/bootstrap-responsive.css')}}
        {{HTML::style('assets/colorpicker/css/colorpicker.css')}}
	<!--Style css-->
        @if(file_exists( base_path().'/assets/stylesheets/stylesheet'.sys_settings().'.css'))
        {{HTML::style('assets/stylesheets/stylesheet'.sys_settings().'.css')}}
        @else
	
        {{HTML::style('assets/css/custom.css')}}
	{{HTML::style('assets/css/style.css')}}
        {{HTML::style('assets/css/lesson.css')}}
        @endif
        <!--Media query css-->
        {{HTML::style('assets/css/media-query.css')}}
        
        @if(file_exists( base_path().'/assets/stylesheets/custom'.sys_settings().'.css'))
            {{HTML::style('assets/stylesheets/custom'.sys_settings().'.css')}}
        @endif
    
    <!--{{HTML::style('assets/css/flexslider.css')}}-->
   
    
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
        <style>
            @if(sys_settings('bgimage')!='' || sys_settings('tagline_background_color'))
            body{
                @if(sys_settings('bgimage')!='')
                    background-image: url("{{url('assets/img/backgrounds/'.sys_settings('bgimage'))}}");
                @endif
                @if(sys_settings('tagline_background_color')!='')
                    background-color: {{sys_settings('tagline_background_color')}};
                @endif
                background-position: center top;
                background-repeat: no-repeat;
            }
            @endif
        </style>
</head>

<body>
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
            	
                <div class="clearfix"></div>
            </div><!--memember-col ends-->
            <div class="navbar navbar-default">   		</div><!--navbar ends-->
        </div><!--container ends-->
    </header><!--header ends-->
    <div class="clearfix"></div>
    @if(isset($meta['header_img_text']))
            <section class="banner">
                <div class="container">
                    <div>
                       <!--	<img src="images/banner/top-banner.jpg" alt="" class="img-responsive" />-->
                        <h2>{{$meta['header_img_text'] or sys_settings('domain')}}</h2>
                    </div>
                </div>
            </section>
    @endif
    @if(Request::url() == url(""))
        {{View::make('pages.announcements')}}      
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
    
	<a class="go-to-top" href="#"></a>
    
    <script src="{{url('jsconfig')}}"></script>
    {{HTML::script('assets/js/modernizr.custom.js')}}
    <!--{{HTML::script('../assets/builds/combined.min.js')}}-->
    {{HTML::script('assets/js/jquery.min.js')}}
    {{HTML::script('assets/js/jquery-ui-1.10.4.custom.min.js')}}
    {{HTML::script('assets/js/bootstrap.min.js')}}
    {{HTML::script('assets/js/bootstrapValidator.min.js')}}
    {{HTML::script('assets/js/bootstrap-growl.js')}}
    {{HTML::script('assets/js/bootbox.min.js')}}
    {{HTML::script('assets/js/moment.js')}}
    {{HTML::script('assets/js/bootstrap-editable.min.js')}}
    {{HTML::script('assets/js/summernote.min.js')}}
    
    {{HTML::script('../assets/js/elastic/elastic.js')}}
    {{HTML::script('../assets/colorpicker/js/bootstrap-colorpicker.js')}}
    {{HTML::script('assets/js/ZeroClipboard.js')}}
    {{HTML::script('assets/js/main.js')}}
    {{HTML::script('../assets/js/custom.js')}}
    {{HTML::script('../assets/js/conversations.js')}}
    {{HTML::script('../assets/js/payment_plans.js')}}
     @if(isset($meta)) 
        {{extra_javascripts($meta)}}
    @endif
    
    <!--<a data-toggle='tooltip' data-placement='right' title='Change Design' class='btn btn-danger do-tooltip' style='position:fixed; top:0px; left:0px;' href="{{url('change_style')}}"><i class='glyphicon glyphicon-refresh'></i></a>-->
</body>
</html>
