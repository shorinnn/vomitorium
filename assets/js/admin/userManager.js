function change_password(url, id){
    bootbox.dialog({
          message: loader_gif,
          title: "Change Password"
        });
    $.get(url,{}, function(html){
          $('.bootbox-body').html(html);
          $('#change_password_form').bootstrapValidator({
              fields: {
                password: {
                    validators: {
                        stringLength: {
                            min: 4,
                            message: 'Password must be at least 4 characters long'
                        }
                    }
                }
            },
            submitHandler:function(validator, form, submitButton){
                show_busy();
                $.post(form.attr('action'), form.serialize(), function(result) {
                    hide_busy();
                    result = parse_json(result);
                    if(!result){
                        $('#change_password_form').data('bootstrapValidator').resetForm();
                        return false;
                    }
                    do_growl(result.text, result.status);
                    if(result.status=='success'){
                        bootbox.hideAll();
                    }
                    else{
                         $('#change_password_form').data('bootstrapValidator').resetForm();
                    }

                });
        }});
    });
}
