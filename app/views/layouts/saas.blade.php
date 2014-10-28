<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="description" content="{{{$meta['pageDescription'] or ''}}}">
        <meta name="keywords" content="{{{$meta['pageKeywords'] or ''}}}">
        <title>{{{ isset($meta['pageTitle']) ? $meta['pageTitle'].' - ' : '' }}}{{sys_settings('domain')}}</title>
        <meta name="author" content="">
        <title>Untitled Page</title>    
        {{HTML::style('assets/font-awesome/css/font-awesome.min.css')}}
        {{HTML::style('assets/css/bootstrap/css/bootstrap.css')}}
        {{HTML::style('assets/css/bootstrap/css/bootstrap-responsive.css')}}
        {{HTML::style('assets/css/flexslider.css')}}
        {{HTML::style('assets/css/editable/css/bootstrap-editable.css')}}
        <!--<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>-->
        {{HTML::style('assets/css/style.css')}}
        {{HTML::style('assets/css/media-query.css')}}
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800' rel='stylesheet' type='text/css'>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{HTML::script('assets/js/ZeroClipboard.js')}}
        {{HTML::style('assets/css/custom.css')}}
        
    </head>

<body>
<body>
    <div class="section">
        <div class="container">
    @yield('content')
        </div>
    </div>
    	<!--Scripts-->
        <script src="{{url('jsconfig')}}"></script>
    {{HTML::script('assets/js/jquery.min.js')}}
    {{HTML::script('assets/js/jquery-ui-1.10.4.custom.min.js')}}
    {{HTML::script('assets/js/bootstrap.min.js')}}
    {{HTML::script('assets/js/bootstrapValidator.min.js')}}
    {{HTML::script('assets/js/bootstrap-growl.js')}}
    {{HTML::script('assets/js/bootbox.min.js')}}
    {{HTML::script('assets/js/moment.js')}}
    {{HTML::script('assets/js/modernizr.custom.js')}}
    {{HTML::script('../assets/js/elastic/elastic.js')}}
    {{HTML::script('../assets/colorpicker/js/bootstrap-colorpicker.js')}}
    {{HTML::script('assets/js/bootstrap-editable.min.js')}}
    {{HTML::script('../assets/js/custom.js')}}
    {{HTML::script('../assets/js/conversations.js')}}
    {{HTML::script('../assets/js/saas.js')}}
     @if(isset($meta)) 
        {{extra_javascripts($meta)}}
    @endif
    
</body>
</html>