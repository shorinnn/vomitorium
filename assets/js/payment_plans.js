function choose_processor(e){
    $('.nodisplay').hide();
    val = $(e.target).val();
    $('.'+val+'-details').show();
}

function save_processor(on_processor_page){
    if($('[name=processor]:checked').length==0){
        do_growl('Please select a payment processor', 'danger');    
        return false;
    }
    if($('[name=processor]:checked').val()=='Stripe'){
        
        if($.trim($('#data-key').val())==''){
            do_growl('Please enter your Stripe key', 'danger');    
            return false;
        }
        name = 'Stripe';
        val = $('#data-key').val();
        val2 = $('#secret-key').val();
    }
    else{
        if($.trim($('#email').val())==''){
            do_growl('Please enter your Paypal email address.', 'danger');    
            return false;
        }
        if($('#email').val()!=$('#confirmation_email').val()){
            do_growl('The confirmation email does not match your Paypal email address.', 'danger');    
            return false;
        }
        name = 'Paypal';
        val = $('#email').val();
        val2 = '';
    }
    $.post(APP_URL+'/payment_plans/processor',{name:name, val:val, val2:val2},function(result){
        result = parse_json(result);
        do_growl(result.text,result.status);
        if(result.status=='success'){
            if(on_processor_page){
                $('[type=text]').val('');
                $('.payment-processor').fadeOut();
                $('tbody').append(result.html);
            }
            else{
                $('#processor').html(name);
                $('.payment-processor').fadeOut(function(){
                    $('.payment-plan').fadeIn();
                });
            }
            
        }
    });
}

function plan_type(e){
    $('.plan-type').hide();
    option = $(e.target).val();
    $('.'+option).show();
}
var has_trial = -1;
function add_trial(e){
    e.preventDefault();
    has_trial *= -1;
    $('.trial .nodisplay').slideToggle();
    var text = $('.trial a').html();
    $('.trial a').html(
        text == "Add Trial Period" ? "Remove Trial" : "Add Trial Period"
    );
    console.log(has_trial);
}

function create_payment_plan(){
    if($('#payment-plan-form #plan-name').val()==''){
        do_growl('Please enter the payment plan name','danger');
        return false;
    }
    if($('#payment-plan-form #plan-type').val()=='subscription'){
        if($('#payment-plan-form #subscription-cost').val() < 0.1 ){
            do_growl('Please enter the subscription cost','danger');
            return false;
        }
        if($('#payment-plan-form #subscription-duration').val() < 1 ){
            unit = $('#payment-plan-form #subscription-unit').val();
            do_growl('Please enter after how many '+unit+' to charge the subscriber again','danger');
            return false;
        }
    }
    else{
        if($('#payment-plan-form #plan-cost').val() < 0.1 ){
            do_growl('Please enter the one-time cost value','danger');
            return false;
        }
    }
    if(has_trial==1){
//        if($('#payment-plan-form #trial-cost').val() < 0.1 ){
//            do_growl('Please enter the trial cost','danger');
//            return false;
//        }
        if($('#payment-plan-form #trial-duration').val() < 1 ){
            unit = $('#payment-plan-form #trial-unit').val();
            do_growl('Please enter after how many '+unit+' the trial is valid for','danger');
            return false;
        }
    }

    $.post(APP_URL+'/payment_plans',$('#payment-plan-form').serialize(),function(result){
        result = parse_json(result);
        do_growl(result.text, result.status);
        if(result.status=='success'){
            bootbox.hideAll();
            $('.payment-plan').fadeOut(function(){
                $('.table').fadeIn();
            });
            $('.table tbody').append(result.html);
        }
    });
}

function show_plan_modal(){
    has_trial = -1;
    $('.trial .nodisplay').hide();
    $('.trial a').html('Add Trial Period');
    $('.one-time').hide();
    $('.subscription').show();
    if($('.payment-plan-form').length==0){
        $form = $('#payment-plan-form').clone();
        $('#payment-plan-form').addClass('payment-plan-form');
        $('#payment-plan-form').removeAttr('id');
    }
    else{
         $form = $('.payment-plan-form').clone();
         $form.attr('id','payment-plan-form');
         $form.removeClass('payment-plan-form');
    }
    $form.find("input[type=text], textarea").val("");
     bootbox.dialog({
      message: loader_gif,
      title: "Create New Payment Plan"
    });
    $('.bootbox-body').html($form);
}

function get_buy_button(e){
    code = $(e.target).attr('data-code');
    bootbox.dialog({
      message: loader_gif,
      title: "Buy Button Code"
    });
    str = '<b>Regular link</b> <textarea rows="2" data-self-title="1" data-clipboard-text=\''+(code)+'\' class="form-control copy-to do-tooltip">'+code+'</textarea> ';
    str += '<br /><b>Affiliate link</b> <textarea rows="2" data-self-title="1" data-clipboard-text=\''+(code)+'?a=AFF_ID_HERE\' class="form-control copy-to do-tooltip">'+code+'?a=AFF_ID_HERE</textarea> ';
    str += '<br /><b>Tracking link</b> <textarea rows="2" data-self-title="1" data-clipboard-text=\''+(code)+'?t=TRACK_ID_HERE\' class="form-control copy-to do-tooltip">'+code+'?t=TRACK_ID_HERE</textarea> ';
    str += '<br /><b>Affiliate & Tracking link</b> <textarea rows="2" data-self-title="1" data-clipboard-text=\''+(code)+'?a=AFF_ID_HERE&t=TRACK_ID_HERE\' class="form-control copy-to do-tooltip">'+code+'?a=AFF_ID_HERE&t=TRACK_ID_HERE</textarea> ';
    $('.bootbox-body').html(str);
    var client = new ZeroClipboard($(".copy-to"));
}