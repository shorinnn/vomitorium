<style>
    /**Universal css**/
    @import url(http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800);
    /*Font Face generator*/
    @font-face {
            font-family: 'HelveticaNeueLTCom-Bd';
            src: url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Bd.eot?#iefix') format('embedded-opentype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Bd.woff') format('woff'),
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Bd.ttf') format('truetype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Bd.svg#HelveticaNeueLTCom-Bd') format('svg');
            font-weight: normal;
            font-style: normal;
    }
    @font-face {
            font-family: 'HelveticaNeueLTCom-Lt';
            src: url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Lt.eot?#iefix') format('embedded-opentype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Lt.woff') format('woff'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Lt.ttf') format('truetype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Lt.svg#HelveticaNeueLTCom-Lt') format('svg');
            font-weight: normal;
            font-style: normal;
    }
    @font-face {
            font-family: 'HelveticaNeueLTCom-Md';
            src: url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Md.eot?#iefix') format('embedded-opentype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Md.woff') format('woff'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Md.ttf') format('truetype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Md.svg#HelveticaNeueLTCom-Md') format('svg');
            font-weight: normal;
            font-style: normal;
    }
    @font-face {
            font-family: 'HelveticaNeueLTCom-MdIt';
            src: url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-MdIt.eot?#iefix') format('embedded-opentype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-MdIt.woff') format('woff'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-MdIt.ttf') format('truetype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-MdIt.svg#HelveticaNeueLTCom-MdIt') format('svg');
            font-weight: normal;
            font-style: normal;
    }
    @font-face {
            font-family: 'HelveticaNeueLTCom-Roman';
            src: url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Roman.eot?#iefix') format('embedded-opentype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Roman.woff') format('woff'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Roman.ttf') format('truetype'), 
                     url('{{url("assets/")}}/fonts/HelveticaNeueLTCom-Roman.svg#HelveticaNeueLTCom-Roman') format('svg');
            font-weight: normal;
            font-style: normal;
    }
    #print_btn, #download_btn{
        border:1px solid red;
        color: #f4f5f5;
        background-color: #52b963;
        border-color: transparent;
        border-radius: 0;
        text-transform: uppercase;
        font-size: 18px;
        font-family: 'HelveticaNeueLTCom-Roman';
        margin: 0px auto;
        cursor: pointer;
    }
    
    #myModal{
        height:100%;
        width:100%;
        position: fixed;
        font-size: 50px;
        background-color:rgba(0, 0, 0, 0.8);
        color:white;
        z-index: 10000000;
        top:0;
        left:0;
        
    }
    
    #myModal h1{
        background-color:black;
        text-indent: 200px;
        margin-top: 200px;
        padding:50px;
    }
</style>
<center>
    @if(sys_settings('installation') != '31-1408525988')
        <button id='print_btn' onclick="print_report({{$report->id}})">Print</button>
    @endif
    <button id='download_btn' onclick="download_report({{$report->id}})">Download</button>
    @if(admin())
        <select onchange='view_report_for(this)'>
            <option value='0'>View Report For User...</option>
            @foreach(User::all() as $user)
                <option value='{{$user->id}}'
                        @if($user->id == Input::get('user'))
                            selected="selected"
                        @endif
                        >{{$user->first_name}} ( {{$user->last_name}} {{$user->email}} )</option>
            @endforeach
        </select>
    @endif
</center>
<div id="the-print-content" style="background-color:white; display:inline-block">
{{$content}}
<p style="font-size:12px;padding-left: 10px; font-weight: bold; clear:both;">
    All content copyright {{sys_settings('title')}} {{date('Y')}} - All rights reserved.
    <span style='float:right; padding-right: 10px;'>Last Updated: {{format_date($report->created_at)}}</span>
</p>
</div>
{{HTML::script('jsconfig')}}
{{HTML::script('assets/js/jquery.min.js')}}
{{HTML::script('assets/js/html2canvas.js')}}
{{HTML::script('assets/js/jquery.cookie.js')}}
<script>
    
    function showModal(){
        $('body').append("<div id='myModal'><h1>Please Wait <img src='../assets/img/ajax-loader_2.gif'/></h1></div>");
    }
function download_report(id){
    showModal();
    $('#download_btn').html( 'Please wait...');
    $('#download_btn').attr('disabled','disabled');
    
    $('#the-print-content').css({'height':'3508px', 'width':'4961px'});
    
    $('.main-box').css('height','100% !important');
    $('.container-fluid').css('height', '95%');
    $('.container-fluid > .row:nth-child(even)').css('height', '90%');
    $('.main-box').attr('style', 'height:100% !important');
    $('div').css( 'font-size','108%' );
    $('.top-3-row').find('.col-xs-4 .box').css('min-height','300px');
    equalizeBoxes();

    w = window.open();
    w.location = APP_URL+'/reports/pre-print/';
    html2canvas($('#the-print-content'), {
        onrendered: function(canvas)
        {
            var img = canvas.toDataURL();
            var token = new Date().getTime(); //use the current timestamp as the token value
            $.post(APP_URL+'/reports/save_report_image', {data: img, id:id}, function(file) {
                console.log(file);
                $('#download_btn').html('Download');
                $('#download_btn').removeAttr('disabled');
                loc = APP_URL+'/reports/print/'+file;
                
                w.location = loc;
                console.log('LOCATION '+loc);
//                window.location = APP_URL+'/reports/print/'+file;

                $('#the-print-content').css({'height':'initial', 'width':'initial'});
                $('.main-box').css('height','initial !important');
                $('.container-fluid').css('height', 'initial');
                $('.container-fluid > .row:nth-child(even)').css('height', 'initial');
                $('.main-box').removeAttr('style');
                $('div').css( 'font-size','100%' );
                $('#myModal').remove();
//                setTimeout(function(){
//                    window.location = window.location.href;
//                },1000)
                fileDownloadCheckTimer = window.setInterval(function () {
//                      var cookieValue = $.cookie('fileDownloadToken');
//                      if (cookieValue == token){
//                          alert('COOKIE VAL IS '+cookieValue);
//                           window.clearInterval(fileDownloadCheckTimer);
//                           window.location = window.location.href;
//                       }
                    $.get(APP_URL+'/download-cookie', function(result){
                          if(result==1){
                            window.clearInterval(fileDownloadCheckTimer);
                            window.location = window.location.href;
                            w.close();
                          }
                          
                      });
                    }, 1000);
            });
        },
        height:3508 ,
        width:4961 
    });
}
//function print_report(id){
//    showModal();
//    $('#download_btn').html( 'Please wait...');
//    $('#download_btn').attr('disabled','disabled');
//    
//    $('#the-print-content').css({'height':'3508px', 'width':'4961px'});
//    $('.main-box').css('height','100% !important');
//    $('.container-fluid').css('height', '95%');
//    $('.container-fluid > .row:nth-child(even)').css('height', '90%');
//    $('.main-box').attr('style', 'height:100% !important');
//    $('div').css( 'font-size','108%' );
//    w = window.open(APP_URL+'/reports/print_loading')
//    html2canvas($('#the-print-content'), {
//        onrendered: function(canvas)
//        {
//            var img = canvas.toDataURL();
//            $.post(APP_URL+'/reports/save_report_image', {data: img, id:id}, function(file) {
//                console.log(file);
//                $('#download_btn').html('Download');
//                 $('#download_btn').removeAttr('disabled');
////                window.location = APP_URL+'/reports/print/'+file;
//                
//                $('#the-print-content').css({'height':'initial', 'width':'initial'});
//                $('.main-box').css('height','initial !important');
//                $('.container-fluid').css('height', 'initial');
//                $('.container-fluid > .row:nth-child(even)').css('height', 'initial');
//                $('.main-box').removeAttr('style');
//                $('div').css( 'font-size','100%' );
//                $('#myModal').remove();
//                w.location = APP_URL+'/reports/print/'+file+'/render';
//            });
//        },
//        height:3508 ,
//        width:4961 
//    });
//}
function print_report(id){
    $('#print_btn').html( 'Please wait...');
    $('#print_btn').attr('disabled','disabled');
    w = window.open(APP_URL+'/reports/print_loading')
    html2canvas($('#the-print-content'), {
        onrendered: function(canvas)
        {
            var img = canvas.toDataURL();
            $.post(APP_URL+'/reports/save_report_image', {data: img, id:id}, function(file) {
                console.log(file);
                $('#print_btn').html('Print');
                $('#print_btn').removeAttr('disabled');
                w.location = APP_URL+'/reports/print/'+file+'/render';
            });
        }
    });
}
var report_segment = "{{$report->slug}}";

function view_report_for(dropdown){
   val = $(dropdown).val();
   if(val>0){
//       window.location = 'strategy-plan?user='+val;
       window.location = report_segment+'?user='+val;
   }
}

function equalizeBoxes(){
    max = 0;
    $('.main-box').each(function(){
        if( $(this).height() > max) max = $(this).height();
    });
    padding = 20;// $('.box-heading').height() + 10;
    defaultMaxHeight =  max;//$('.main-box').height();
    lastVal = 0;
    $('.main-box').each(function(){
        maxHeight = max;
        currentHeight = 0;
        $(this).find('.box-content .box').each(function(){
           currentHeight += $(this).height(); 
        });
        while( currentHeight < maxHeight){
            $(this).find('.box-content .box').each(function(){
               $(this).height( $(this).height() + 5); 
               $(this).attr('data-remove-style', 1);
            });
            currentHeight = 0;
            
            $(this).find('.box-content .box').each(function(){
               currentHeight += $(this).height(); 
            });
            currentHeight += padding * $(this).find('.box-content .box').length;
        }
        if(lastVal == 0 ){
            lastVal = $(this).find('.box-content .box').last().offset().top + $(this).find('.box-content .box').last().height();
        }
        else{
            dif = lastVal - ( $(this).find('.box-content .box').last().offset().top + $(this).find('.box-content .box').last().height() );
            $(this).find('.box-content .box').last().height( $(this).find('.box-content .box').last().height() + dif);
        }
    });
}
</script>
<div style="opacity: 0">
<img src="../assets/img/ajax-loader_2.gif" />
</div>