var loader_gif = '<p class="text-center"><img src="' + APP_URL + '/assets/img/ajax-loader.gif" /></p>';
function del(id){
    $('.list-'+id).remove();
    do_growl('Account deleted','success');
}

function suspend(id){
    do_growl('Account suspended','success');
    $('.list-'+id+' .suspend').hide();
    $('.list-'+id+' .activate').show();
    $('.list-'+id).addClass('suspended');
}


function activate(id){
    do_growl('Account activated','success');
    $('.list-'+id+' .activate').hide();
    $('.list-'+id+' .suspend').show();
    $('.list-'+id).removeClass('suspended');
}

var callbacks = new Array();


function ajax_link(url, message, callback, request_type){
    if(typeof request_type==='undefined') request_type = 'GET';
    if(!confirm( message )) return false;
    show_busy();
    $.ajax({
        url: url,
        type: request_type,
        success: function(result) {
            hide_busy();
            eval(callback);
        }
    });
}

function admin_info(id){
    bootbox.dialog({
      message: loader_gif,
      title: "Admin Details"
    });
    $('.bootbox-body').load(APP_URL+'/accounts/admin/'+id, function(){
//        $('.editable').editable();
    });  
}

function prepopulate_fields(){
    domain = $('#subdomain').val();
    $('#db_name').val('db_'+domain);
    $('#db_username').val('user_'+domain);
    num = Math.random().toString();
    num = num.substring(2, num.length-1);
    $('#db_pass').val(domain + num);
}
$(document).ajaxComplete(function(){
    $('[data-tooltip=1]').tooltip();
    $('.editable').editable();
});

$(function(){
    url = APP_URL;
    APP_URL = APP_URL.split('.');
    if(document.URL.indexOf('www')==-1)    APP_URL = '//'+APP_URL[APP_URL.length-2]+'.'+APP_URL[APP_URL.length-1];
    else     APP_URL = '//www.'+APP_URL[APP_URL.length-2]+'.'+APP_URL[APP_URL.length-1];
    $('[data-tooltip=1]').tooltip();
    $('#account_form').submit(function(){
        show_busy();
        $.post($('#account_form').attr('action'), $('#account_form').serialize(), function(result){
            console.log(result);
            hide_busy();
            result = parse_json(result);
            if (!result) return false;
            if(result.status=='danger'){
                do_growl(result.text,'danger');
            }
            else{
               $('#account_form')[0].reset();
               $('table').append(result.text);
               do_growl('Account created','success'); 
               console.log(result.text);
            }
            
        });
        return false;
    });
});