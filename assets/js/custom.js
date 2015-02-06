// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};

Array.prototype.pluck = function(val) {
    ret = false;
    for(var i = this.length; i--;){
          if (this[i] == val) ret = this.splice(i, 1);
      }
    return ret;
};

function enable_rte(toolbar) {
    if(toolbar==1){
        toolbar = [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['para', ['paragraph']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['lists', ['ul', 'ol']],
            ['insert', ['link', 'picture']],
            ['misc', ['codeview']]
        ];
    }
    else if(toolbar==3){
        toolbar = [
            ['style', ['bold', 'italic', 'underline']],
            ['lists', ['ul', 'ol']],
            ['insert', ['link', 'picture']]
        ];
    }
    else{
        toolbar = [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['para', ['paragraph']],
            ['color', ['color']],
            ['fontname', ['fontname','fontsize']],
            ['insert', ['link', 'picture']]
        ];
    }
    $('.summernote_editor').summernote({
        minHeight: 140,
        toolbar: toolbar,
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0],editor,welEditable);
        }
    });
    $('[data-original-title=Style]').html('Styles <span class="caret"></span>');
}

$(function() {
    
    $('body').on('keyup', '.linked-cell', function(){
        dataLink = $(this).attr('data-link-class');
        order = $(this).attr('data-link-order');
        if( typeof(dataLink) !='undefined' ){
            val = $(this).val();
            $('[data-link-class="'+dataLink+'"]').each(function(){
                if( $(this).attr('data-link-order') > order ) $(this).val( val );
            });
        }
    });
    
    if(typeof(enable_autosave) !='undefined' && enable_autosave===1) enable_autosave_lesson();
    
    $('body').on('click','.note-editable', function(e){
        $(e.target).find('.summernote_placeholder').remove();
    });
    
    $('body').on('keyup','input.two-column', add_two_column_row);
    
    $('body').on('keyup','.block-score', update_block_score);
    if(typeof(onload_functions)!='undefined'){
        $(onload_functions).each(function(index, func){
            window[func]();
        });
    }
    
    
    $( "[data-toggle=combobox]" ).combobox();
    ZeroClipboard.config( { swfPath: APP_URL+"/ZeroClipboard.swf" } );
    ZeroClipboard.on("aftercopy", function(e) {
        console.log('copied');
        if($(e.target).attr('data-self-title')==1){
            input = $(e.target);
             $('.copied').remove();
            input.after('<span class="copied" style="font-weight:bold; color:green">Copied to clipboard!</span>');
            $('.copied').fadeOut(2000);
            return false;
        }
        input = $(e.target).parent().find('input').first();
        input.attr('title','Copied to clipboard!');
        input.tooltip();
        input.trigger('mouseover');
        setTimeout(function(input){
            input.tooltip('destroy');
            input.removeAttr('title');
        }, 1000, input);
    });    
    
    enable_copy_to_clipboard();
    $('#attachment, #comment_attachment').change(upload_attachment);
    
    $('body').on('click','.selectable-txt', function(e){
         $(this).select();
         $(this).next('.copy-to').trigger('click');
    });
    if(typeof (do_enable_rte)!='undefined') {
        if(typeof (rte_config)!='undefined')enable_rte(rte_config);
        else        enable_rte(1);
    }
    $('body').on('submit','.ajax-form',process_ajax_form);
    $( ".datepikr" ).datepicker();
    
    $('body').on('click','.dash-content .pagination a', function(){
        dash_content = $(this).parent().parent().parent();
        dash_content.addClass('disabled');
        $.get($(this).attr('href'), function(result){
            fade_content(dash_content, result);
            dash_content.removeClass('disabled');
        });
       return false; 
    });
    
    $("*").on("remove", tooltip_destroy);
    
    $('.sortable').sortable();
    $('.colorpicker').colorpicker().on('hide', function(ev){
       console.log();
       $(ev.currentTarget).find('input').trigger('change');
    });
    
    if(typeof(registered_listeners)!='undefined'){
        $(registered_listeners).each(function(index,evt){
            $(evt.target).on(evt.event, window[evt.callback]);
        });
    }
    $('body').on('submit', '.update_form', do_update_form);
    $('body').on('click', '.delete-btn', confirm_delete);
    $('body').on('click', '.confirm-delete-btn', mod_delete);
    // ready
    equalize_cells();
    $('#create_form').submit(create_add_to_list);
    if (window.location.hash) {
        id = window.location.hash;
        if ($(id).is(':visible') == false && $(id).length>0 ) {
            
            var classList = $(id).parent().parent().parent().attr('class').split(/\s+/);
            $.each(classList, function(index, item) {
                if (item.indexOf('section-') > -1) {
                    page = item.split('-');
                    page = page[1];
                    quick_section(page, total_sections);
                    section_scroll_to = id;

                }
            });
        }
    }
    $('#search').on('keyup', function(event) {
        if ($('#search').val() == '') {
            forced_pagination_url = $(location).attr('href');
            pagination_load_page();
            return false;
        }
        skip_timeout = true;
        show_busy();
        skip_timeout = false;
        $.post($('#search').attr('data-url'), {search: $('#search').val()}, function(result) {
            $('#ajax-content').html(result);
            $('.editable').editable();
            $('.do-tooltip').tooltip({container: 'body'});
            hide_busy();
        });
    });

    $('body').tooltip({
        selector: '.do-tooltip',
        container: 'body',
        title: function(){return $(this).attr('data-title-noconflict');}
    });
    
    $('body').popover({
        selector: '.do-popover',
        container: 'body'
    });
    // lesson validation
    $('#lesson_form').submit(function() {
        is_valid = true;
        // see if any scales need attention
        if($('.scale-value').length>0){
            do_growl("Please rate all the items on this page",'danger');
            $('html body').animate({
                    scrollTop: $('.scale-value').first().offset().top - 10
                }, 700, 'linear', function() {
                    $('.scale-value').first().parent().effect("highlight", {}, 2000);
                });
            return false;
        }
        if($('.sortable-answer').length>0 && $('.sortable-answer[value=""]').length > 0){
            do_growl("Please sort and submit your selection",'danger');
            $('html body').animate({
                    scrollTop: $('.sortable-answer[value=""]').first().parent().offset().top - 10
                }, 700, 'linear', function() {
                    $('.sortable-answer[value=""]').first().parent().effect("highlight", {}, 2000);
                });
            return false;
        }
        $('.mc_validation').each(function() {
            max = $(this).attr('data-mc-max');
            min = $(this).attr('data-mc-min');
            block = $(this).attr('data-mc-block');
            actual = $('.mc-answer-' + block + ':checked').length;
            if (actual > max || actual < min) {
                do_growl($(this).attr('data-mc-message'), 'danger');
                is_valid = false;
                go_to_section = $('#block-' + block).parent().parent().attr('data-section');
                if (go_to_section == 'undefined' || typeof go_to_section === 'undefined')
                    go_to_section = $('#block-' + block).parent().parent().parent().attr('data-section');
                if (typeof go_to_section !== 'undefined' && !isNaN(go_to_section) && quick_page != go_to_section) {
                    quick_section_no_scroll = 'block-' + block;
                    quick_section(go_to_section, total_sections);
                }
                else
                    validation_scroll_to('block-' + block);
                return false;
            }
        });
        $('.required').each(function() {
            $('.required').css('outline','initial');
            if ($.trim($(this).val()) == '') {
                $('.required').filter(function() {return !this.value;}).css('outline', 'red solid thin');
                console.log($(this).css);
                do_growl('Please fill in all required fields', 'danger');
                is_valid = false;
                return false;
            }
        });
        if (is_valid == false)
            return false;

        show_busy();
        $('.ui-sortable').each(function() {
            id = $(this).attr('id');
            console.log(id);
            block_number = id.split('-');
            block_number = block_number[1];
            submit_sortable_list(block_number);
        });

        $('.lesson-success').remove();
        url = $('#lesson_form').attr('action');
        console.log($('#lesson_form').serialize());
        $.post(url, $('#lesson_form').serialize(), function(data) {
            data = parse_json(data);
            console.log(data);
            $('.submit-btns').first().before('<p class="alert alert-success lesson-success">' + data.text + '</p>');
            if ($('.next-lesson-btn:visible').length == 0) {
                console.log('adding next lsn' + data.button);
                if ($('.submit-btns:visible').length > 0)
                    $('.submit-btns').first().after(data.button);
                else
                    $('.lesson-success').first().after(data.button);
            }
            $('.submit-btns').remove();
            hide_busy();
            $('.white-textarea').prop("disabled", true);
        });
        return false;
    });
    // contact form
    $('#contact_form').bootstrapValidator({submitHandler: function(validator, form, submitButton) {
            $.post(form.attr('action'), form.serialize(), function(result) {
                result = parse_json(result);
                if (!result)
                    return false;
                do_growl(result.text, result.status);
                if (result.status == 'success') {
                    $('#contact_form').data('bootstrapValidator').resetForm();
                    $('#contact_form')[0].reset();
                }

            });
        }});

    // ajax pagination
    if ($('#ajax-content').length > 0) {
        $('body').on('click', '.pagination a', pagination_load_page);
    }

    $('.editable').editable();
});

function update_color_field(elem, val){
    $(elem).val(val);
    $(elem).trigger('change');
    $(elem).parent().colorpicker('setValue', val);
}
function ajax_update(element){
    element = $(element);
    val = element.val();
    if( typeof(element.attr('data-code'))!='undefined' ){
        $src = $(element.attr('data-code'));
        val = $src.code(); 
    }
    $.post(element.attr('data-url'),{ 
            pk: element.attr('data-pk'), 
            name: element.attr('data-field'), 
            value: val
        }, function(){
            element.effect("highlight", {}, 500);
        }
    );
}
function tooltip_destroy(){
    $('.in:visible').hide();
    $("*").off("remove");
    setTimeout(function(){
        $("*").on("remove", tooltip_destroy);
    },100);
}
function set_unload_warning(e){
    if( $('[type="text"]:visible').length > 0){
        window.onbeforeunload = notify_name_change;
    }
    else{
        window.onbeforeunload = false;
    }
    if(e!=null){
        setTimeout(set_unload_warning,200, null);
    }
}
function notify_name_change(e){
    console.log(  $('[type="text"]:visible').length  );
    return 'You are about to leave this page but have not saved your data. You will lose your progress.';
}
var loader_gif = '<p class="text-center"><img src="' + APP_URL + '/assets/img/ajax-loader.gif" /></p>';
$.fn.editable.defaults.onblur = 'ignore';

function do_growl(text, the_type) {
    if(the_type=='danger') the_type='warning';
    
    $.growl(text, {
        type: the_type,
        allow_dismiss: true,
        position: {
            from: "top",
            align: "center"
        },
        z_index: 9999,
        template: {
            container: '<div class="col-md-3 growl-animated alert">'
        }
    });

}

function parse_json(result) {
    try {
        result = $.parseJSON(result);
    }
    catch (e) {
        hide_busy();
        console.log('PARSE_JSON() param:' + result);
        do_growl('An error occurred', 'danger');

        return false;
    }
    return result;
}
var section_scroll_to = '';

function validation_scroll_to(id) {
    $('html body').animate({
        scrollTop: $('#' + id).offset().top
    }, 700, 'linear', function() {
        $('#' + id).effect("highlight", {}, 2000);
    });
}

delete_interval = 3000;
initial_width = 0;
function confirm_delete(){
    initial_width = $(this).outerWidth();
    $(this).toggleClass('delete-btn');
    var the_width = '145px';
    if($(this).hasClass('btn-sm')) the_width = '125px';
    if($(this).attr('data-width')!='') the_width = $(this).attr('data-width');
    confirm_text = ' Confirm Delete';
    if(typeof($(this).attr('data-confirm-text'))!='undefined' && $(this).attr('data-confirm-text')!='') confirm_text = $(this).attr('data-confirm-text');
    $(this).animate({
        width: the_width
    },100,function(){
        $(this).toggleClass('confirm-delete-btn');
        //$(this).html($(this).html()+' Delete?');
        $(this).html('<i class="glyphicon glyphicon-remove"></i>'+confirm_text);
        setTimeout(cancel_delete, delete_interval, this);
    });
    
}

function cancel_delete(identifier){
    $(identifier).animate({
        width: initial_width
    },100, function(){
        $(identifier).toggleClass('delete-btn');
        $(identifier).toggleClass('confirm-delete-btn');
        $(identifier).html("<i class='glyphicon glyphicon-trash'></i>");
    });
}

function mod_delete(){
    target = $(this).attr('data-target');
    id = $(this).attr('data-id');
    url = $(this).attr('data-url');
    $(this).attr('disabled','disabled');
    $.ajax({
        url: url,
        type: 'DELETE',
        success: function(result) {
            result = parse_json(result);
            if(result.status!='success'){
                do_growl('An Error Occurred - please refresh the page and try again', 'danger');
            }
            $('.'+target+'-'+id).remove();
        },
        error: function(){
            do_growl('An Error Occurred - please refresh the page and try again', 'danger');
        }
    });
    
}

var forced_pagination_url = '';
function pagination_load_page(event) {
    try {
        event.preventDefault();
    }
    catch (e) {
    }
    if ($(this).attr('href') != '#') {

        skip_timeout = true;
        show_busy();
        skip_timeout = false;
        $('.editable').editable('option', 'disabled', true);
        $('#ajax-content').fadeTo('fast', .6);
        $('#ajax-content').append('<div class="grayed_out"></div>');
        $('#ajax-content').find("*").prop("disabled", true);
        $('.pagination').append(' <img class="pagination-loader" src="' + APP_URL + '/assets/img/ajax-loader.gif" />');
        url = forced_pagination_url == '' ? $(this).attr('href') : forced_pagination_url;
        forced_pagination_url = '';
        console.log($(this));
        old_page = current_ajax_page();
        $('#ajax-content').load(url, function() {
            hide_busy();
            $('.editable').editable();
            $('.do-tooltip').tooltip({container: 'body'});
            $('#ajax-content').find("*").prop("disabled", false);
            $('#ajax-content').fadeTo('fast', 1);
            // pagination_animation(url, old_page);
            enable_copy_to_clipboard();
        });
    }
}


function current_ajax_page() {
    var page = $('.pagination .active').first().text();
    if (page < 1)
        page = 1;
    return page;
}

function fade_content(identifier, content){
    $(identifier).fadeOut(300,function(){
            $(identifier).html( content );
            $(identifier).fadeIn(300);
        });
}

function del(id, url) {
    bootbox.confirm("Are you sure?", function(result) {
        if (result == true) {
            show_busy();
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(result) {
                    hide_busy();
                    result = parse_json(result);
                    if (!result)
                        return false;
                    do_growl(result.text, result.status);
                    if (result.status == 'danger')
                        return false;
                    
                    $('.list-row-' + id).remove();
                    if($('.block').length==0) $('.initial_area').show();
      
                    if (typeof is_lesson_editor !== "undefined" && is_lesson_editor) {
                        return true;
                    }
                    
                    
                    if ($('.list-row').length == 0) {
                        if ($('.pagination').length > 0 && $('.pagination .active').html() != '<span>1</span>') {
                            $('.pagination a').first().click();
                        }
                        // on first page, refresh first page
                        else {
                            forced_pagination_url = $(location).attr('href');
                            pagination_load_page();
                        }
                    }
                    else {
                        forced_pagination_url = $(location).attr('href') + "?page=" + current_ajax_page();
                        pagination_load_page();
                    }
                }
            });
        }
    });
}

original_btn_label = 'Add New';
function create_lesson_form(){
    create_form();
    $('#add_form_a').removeAttr('onclick');
}
function create_form() {
    $('.add_form').slideToggle('fast', function() {
        if ($('.create-form-btn').html() != 'Hide This')
            original_btn_label = $('.create-form-btn').html();

        $('.create-form-btn').html(
                $('.add_form').is(':visible') ? "Hide This" : original_btn_label
                );

        $('#add_form').bootstrapValidator({
            submitHandler: function(validator, form, submitButton) {
                show_busy();
                $.post(form.attr('action'), form.serialize(), function(result) {

                    result = parse_json(result);
                    if (!result) {
                        $('#add_form').data('bootstrapValidator').resetForm();
                        hide_busy();
                        return false;
                    }
                    do_growl(result.text, result.status);
                    if (result.status == 'success') {
                        if (typeof result.redirect_url !== "undefined" && result.redirect_url) {
                            show_busy();
                            window.location = result.redirect_url;
                            return;
                        }
                        $('.add_form').slideToggle('fast');
                        forced_pagination_url = $(location).attr('href');
                        pagination_load_page();
                        $('#add_form').data('bootstrapValidator').resetForm();
                        $('#add_form')[0].reset();
                        $('.create-form-btn').html('Add New');
                    }
                    else {
                        $('#add_form').data('bootstrapValidator').resetForm();
                    }
                    hide_busy();
                });
            }});
    });
}

function move_item(url) {
    show_busy();
    $.get(url, {}, function(response) {
        hide_busy();
        forced_pagination_url = $(location).attr('href') + "?page=" + current_ajax_page();
        pagination_load_page();
    });
}
var error_timer;
var skip_timeout = false;
function show_busy() {
    if (skip_timeout == false)
        error_timer = setTimeout(ajax_error, 5000);
    $('body').append('<div id="busy-holder"><div id="busy"></div></div>');
    $('#busy').show();
    skip_timeout = false;
}

function hide_busy() {
    clearInterval(error_timer);
    error_count = 0;
    $('#busy-holder').remove();
}
var error_count = 0;
function ajax_error() {
    if (error_count == 0) {
        do_growl('The requested operation is taking longer than usual to perform - Please wait...', 'danger');
        error_count++;
        setTimeout(ajax_error, 5000);
    }
    else {
        do_growl('Please refresh the page and retry the operation.', 'danger');
    }
}


function all_scales(block) {
    var master_list = [];
    var is_valid = true;
    $('.scale-holder-' + block + ' .col-lg-12').each(function() {
        input_name = $(this).attr('data-input-name');
        if(typeof(input_name) != 'undefined'){
            entry = $(this).attr('data-entry-name');
            var vl = $('[name=' + input_name + "]:checked").val();
            if (isNaN(vl)) {
                console.log(vl + " name "+input_name);
                vl = "0";
                is_valid = false;

            }
            master_list.push({key: vl, val: entry});
        }
    });
    if (!is_valid) {
        do_growl('Please rate all items', 'danger');
        return false;
    }
    master_list = master_list.sort(function(a, b) {
        return a.key - b.key;
    });
    master_list.reverse();
    console.log(master_list.length);
    str = '<ul id="sortable">';
    for (i = 0; i < master_list.length; ++i) {
        str += '<li class="ui-state-default" data-key="' + master_list[i].key + '"><i class="glyphicon glyphicon-resize-vertical"></i> ' + master_list[i].val + "</li>";
        if (i > sortable_count - 2)
            break;
    }
    str += '</ul><input type="hidden" value="" class="sortable-answer" name="sortable-answer[' + block + ']" id="sortable-answer-' + block + '" /><button type="button" class="btn btn-primary" onclick="submit_sorted(' + block + ')">Submit</button>';
    $('.scale-holder-' + block).html(str);
    $("#sortable").sortable({
        placeholder: "ui-state-highlight"
    });
    $("#sortable").disableSelection();
}

function submit_sorted(block) {
    str = '';
    data = new Array();
    var val = $('.ui-state-default').each(function() {
        text = $.trim($(this).text());
        key = $.trim($(this).attr('data-key'));
        data.push({rated: key, option: text});
    });
    str = JSON.stringify(data);
    $('#sortable-answer-' + block).val(str);
    console.log($('#sortable-answer-' + block).val());
    //$('#lesson_form').submit();
    $('#block-'+block+' button').after('Submitted');
    $('#block-'+block+' button').hide();
}


function mark_submission_attended(message) {
    show_busy();
    $.post(APP_URL + '/mark_submission_attended', {message: message}, function(result) {
        hide_busy();
        $('#mark-s-read-' + message).html('Mark as not yet reviewed');
        $('#mark-s-read-' + message).addClass('red-button');
        $('#mark-s-read-' + message).removeClass('greenyellow-button');
        //$('#mark-s-read-'+message).removeClass('btn-success');
        //$('#mark-s-read-'+message).addClass('btn-danger');
        $('#unattended-warning-' + message).remove();
        //$('#red-btn-'+message).remove();
        $('#red-btn-' + message).addClass('invisible');
        $('#mark-s-read-' + message).attr('onclick', 'mark_submission_unattended(' + message + ')');
 
        if ($('.unattended_item').length == 0 && $('.unattended_item_visited').length == 0) {
            $('#next_item_btn').hide();
        }
    });
}

function mark_submission_unattended(message) {
    show_busy();
    $.post(APP_URL + '/mark_submission_unattended', {message: message}, function(result) {
        hide_busy();
        $('#mark-s-read-' + message).html('<i class="glyphicon glyphicon-ok"></i> Mark as reviewed');
        $('#mark-s-read-' + message).removeClass('red-button');
        $('#mark-s-read-' + message).addClass('greenyellow-button');
        $('#mark-s-read-' + message).attr('onclick', 'mark_submission_attended(' + message + ')');
        $('#next_item_btn').show();
    });
}



function upload_avatar() {
    skip_timeout = true;
    show_busy();
    skip_timeout = false;
    data = new FormData();
    var fileSelect = document.getElementById('file');
    var file = fileSelect.files[0];
    if (typeof file == 'undefined') {
        do_growl('Please select a file to upload.', 'danger');
        hide_busy();
        return false;
    }

    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL + "/settings",
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            hide_busy();
            if (response == 'error') {
                do_growl('Error saving image', 'danger');
                return false;
            }
            do_growl('Upload successful', 'success');
            $('.discussion-thumb').attr('src', response);
            var control = $("#file");
            control.replaceWith(control = control.clone(true));

        }
    });
}

function show_tab(id) {
    $('#tb' + id).click();
}

function edit_answers() {
    $('#edit_btn').hide();
    $('#submit_btn').show();
    $('input').removeAttr('disabled');
    $('input').prop('disabled', false);
    $('textarea').removeAttr('disabled');
    $('textarea').prop('disabled', false);
    $('#scale_answer_area').hide();
    $('.scale-holder').show();
    needs_edit = false;
}


function next_unattended_item() {
    if ($('.unattended_item').length == 0) {
        bootbox.confirm("Do you want to go back to the first not yet reviewed item?", function(result) {
            if (result == false)
                return;
            $('.unattended_item_visited').addClass('unattended_item');
            $('.unattended_item_visited').removeClass('unattended_item_visited');
            next_unattended_item();
        });
        return false;
    }
    $('html body').animate({
        scrollTop: $('.unattended_item').first().offset().top
    }, 500);
    $('.unattended_item').first().effect("highlight", {}, 3000);
    $('.unattended_item').first().addClass('unattended_item_visited');
    $('.unattended_item').first().removeClass('unattended_item');
}

function user_attended(user) {
    result = confirm("Do you want to mark all submissions and comments from this user as reviewed?");
    if (result == true) {
        show_busy();
        $.post(APP_URL + '/mark_user', {user: user}, function(result) {
            hide_busy();
            $('.mark_user').hide();
            $('.unattended').hide();
            $('.unattended_item').hide();
            $('.unattended_item_visited').hide();
            $('#next_item_btn').hide();
            $('#mark_lesson_btn').hide();
            do_growl('User has been marked as reviewed', 'success');
            window.location = APP_URL;
        });
    }
}

function lesson_attended(lesson, user) {
    result = confirm("Do you want to mark all submissions and comments in this lesson as reviewed?");
    if (result == true) {
        show_busy();
        $.post(APP_URL + '/mark_lesson', {lesson: lesson, user: user}, function(result) {
            hide_busy();
            $('.unattended').hide();
            $('.unattended_item').hide();
            $('.unattended_item_visited').hide();
            $('#next_item_btn').hide();
            $('#mark_lesson_btn').hide();
            do_growl('Lesson has been marked as reviewed', 'success');
            if ($('.next-lesson').length > 0) {
                console.log($('.next-lesson').length);
                href = $('.next-lesson').attr('href');
                window.location = href;
            }
        });
    }
//    });

}

function toggle_block(id) {
    $('#' + id + ' .panel-body').toggle('fast', function() {
        if ($('#' + id + ' .panel-body').is(':visible')) {
            $('#' + id + ' .toggle-block-btn').removeClass('glyphicon-resize-full');
            $('#' + id + ' .toggle-block-btn').addClass('glyphicon-resize-small');
        }
        else {
            $('#' + id + ' .toggle-block-btn').removeClass('glyphicon-resize-small');
            $('#' + id + ' .toggle-block-btn').addClass('glyphicon-resize-full');
        }
    });
}


function strip_tags(string){
    string = string.replace(/(<([^>]+)>)/ig,"");
    return string;
}

function scrollto(id) {
    $('html body').animate({
        scrollTop: $('#' + id).offset().top
                //scrollTop: $('.pagination').first().offset().top
    }, 1000);
}

function page_validation(current_page) {
    is_valid = true;
    $('.section-' + current_page + ' .mc_validation').each(function() {
        max = $(this).attr('data-mc-max');
        min = $(this).attr('data-mc-min');
        block = $(this).attr('data-mc-block');
        actual = $('.mc-answer-' + block + ':checked').length;
        if (actual > max || actual < min) {
            do_growl($(this).attr('data-mc-message'), 'danger');
            is_valid = false;
            go_to_section = $('#block-' + block).parent().parent().attr('data-section');
            if (go_to_section == 'undefined' || typeof go_to_section === 'undefined')
                go_to_section = $('#block-' + block).parent().parent().parent().attr('data-section');
            if (typeof go_to_section !== 'undefined' && !isNaN(go_to_section) && quick_page != go_to_section) {
                quick_section_no_scroll = 'block-' + block;
                quick_section(go_to_section, total_sections);
            }
            else
                validation_scroll_to('block-' + block);
            return false;
        }
    });

    $('.section-' + current_page + ' .required').each(function() {
        if ($.trim($(this).val()) == '') {
            do_growl('Please fill in all required fields', 'danger');
            is_valid = false;
            return false;
        }
    });

    return is_valid;
}
quick_page = 0;
var quick_section_no_scroll = false;
function quick_section(next_quick_page, max) {
    current_page = quick_page;
    // validation only when going foward
    if (next_quick_page > current_page && needs_edit == false) {
        if (!page_validation(current_page))
            return false;
    }

    $('.section-' + current_page).fadeOut(function() {

        current_page = next_quick_page;
        $('.section-' + current_page).fadeIn();
        $('.section_nav').html('');
        if (current_page > 0) {
            $('.section_nav').append(" <button type='button' class='btn btn-primary page-btn' onclick='next_section_page(" + current_page + ",\"prev\" , " + max + ")'>Previous Step</button>");
        }
        if (current_page < max - 1) {
            $('.section_nav').append(" <button type='button' class='btn btn-primary page-btn' onclick='next_section_page(" + current_page + ",\"next\",  " + max + ")'>Next Step</button>");
        }
        if (current_page == max - 1)
            $('.submit-btns').removeClass('hidden');
        else
            $('.submit-btns').addClass('hidden');

        $('.pag-' + quick_page).removeClass('active');
        $('.pag-' + next_quick_page).addClass('active');
        quick_page = current_page;
        if (quick_section_no_scroll !== false) {
            validation_scroll_to(quick_section_no_scroll);
            quick_section_no_scroll = false;
            return false;
        }
        $('html,body').stop(true,true).animate({
            scrollTop: $('.top-pagination').offset().top
            //scrollTop: $('.section-' + current_page).offset().top
                    //scrollTop: $('.pagination').first().offset().top
        }, 1000);
        if (section_scroll_to != '') {
            setTimeout(function() {
                $('html,body').stop(true, true).animate({
                    scrollTop: $(section_scroll_to).offset().top
                }, 1000);
                section_scroll_to = '';
            }, 1005);
        }

    });

}

function next_section_page(current_page, dir, max) {
    if (dir == 'next')
        current_page++;
    else
        current_page--;
    quick_section(current_page, max);
    return false;
    if (!page_validation(current_page))
        return false;
    $('.section-' + current_page).fadeOut(function() {
        if (dir == 'next')
            current_page++;
        else
            current_page--;
        $('.section-' + current_page).fadeIn();
        $('.section_nav').html('');
        if (current_page > 0) {
            $('.section_nav').append(" <button type='button' class='btn btn-primary page-btn' onclick='next_section_page(" + current_page + ",\"prev\" , " + max + ")'>Previous</button>");
        }
        if (current_page < max - 1) {
            $('.section_nav').append(" <button type='button' class='btn btn-primary page-btn' onclick='next_section_page(" + current_page + ",\"next\",  " + max + ")'>Next</button>");
        }
        if (current_page == max - 1)
            $('.submit-btns').removeClass('hidden');
        else
            $('.submit-btns').addClass('hidden');
        $('html body').animate({
            scrollTop: $('.section-' + current_page).offset().top
        }, 1000);
    });

}

function load_dynamic_answers(cat_id, block_id) {
    $('#ajax-' + block_id).html(loader_gif);
    $.get(APP_URL + '/dynamic_answers/' + cat_id, function(data) {
        $('#ajax-' + block_id).html(data);
    });
}

function do_slide_toggle(id) {
    $(id).slideToggle();
}

function do_sortable_list(block) {
    block_id = block;
    is_valid = true;
    id = "sortable-list-" + block;
    $('.mc_validation').each(function() {
        block = $(this).attr('data-mc-block');
        if (block == id) {
            max = $(this).attr('data-mc-max');
            min = $(this).attr('data-mc-min');
            actual = $('.' + block + ':checked').length;
            if (actual > max || actual < min) {
                do_growl($(this).attr('data-mc-message'), 'danger');
                is_valid = false;
                return false;
            }
        }
    });
    if (is_valid == false){
        return false;
    }
        

    var master_list = [];
    $('.sortable-list-holder-' + block_id + ' [type=checkbox]:checked').each(function() {
        master_list.push({key: $(this).val(), val: $(this).val()});
    });

    $('.sorting-instructions-' + block_id).show();
    $('.sortable-list-holder-' + block_id).html('');
    str = '<ul class="sortable" id="sortable-' + block_id + '">';
    for (i = 0; i < master_list.length; ++i) {
        str += '<li class="ui-state-default" data-key="' + master_list[i].key + '"><i class="glyphicon glyphicon-resize-vertical"></i> ' + master_list[i].val + "</li>";
        if (i > sortable_count - 2)
            break;
    }
    //str +='</ul><input type="hidden" name="sortable-answer['+block_id+']" id="sortable-list-answer-'+block_id+'" /><button type="button" class="btn btn-primary" onclick="submit_sortable_list('+block_id+')">Submit</button>';
    str += '</ul><input type="hidden" name="sortable-answer[' + block_id + ']" id="sortable-list-answer-' + block_id + '" />';
    console.log(str);
    $('.sortable-list-holder-' + block_id).html(str);
    $("#sortable-" + block_id).sortable({
        placeholder: "ui-state-highlight"
    });
    $("#sortable-" + block_id).disableSelection();
    $('html body').animate({
        scrollTop: $('#block-' + block_id).offset().top
    }, 1000);
}

function submit_sortable_list(block) {
    str = '';
    data = new Array();
    var val = $('#sortable-' + block + ' >.ui-state-default').each(function() {
        text = $.trim($(this).text());
        key = $.trim($(this).attr('data-key'));
        data.push({rated: key, option: text});
    });
    str = JSON.stringify(data);
    $('#sortable-list-answer-' + block).val(str);
    console.log($('#sortable-list-answer-' + block).val());
    
    // $('#lesson_form').submit();
}

function edit_sortable(block) {
    $('.sortable-list-holder-' + block + ' .mc_validation-disabled').addClass('mc_validation');
    $('.sortable-list-holder-' + block + ' .mc_validation-disabled').removeClass('mc_validation-disabled');
    $('.scale-answered-' + block).remove();
    $('.sortable-list-holder-' + block).removeClass('hidden');
    edit_answers();
}

function upload_background() {
    skip_timeout = true;
    show_busy();
    skip_timeout = false;
    data = new FormData();
    var fileSelect = document.getElementById('file');
    var file = fileSelect.files[0];
    if (typeof file == 'undefined') {
        do_growl('Please select a file to upload.', 'danger');
        hide_busy();
        return false;
    }

    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL + "/background",
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            hide_busy();
            if (response == 'error') {
                do_growl('Error saving image', 'danger');
                return false;
            }
            do_growl('Upload successful', 'success');
            $('#bg-preview').attr('src', response);
            var control = $("#file");
            control.replaceWith(control = control.clone(true));

        }
    });
}

function upload_logo() {
    skip_timeout = true;
    show_busy();
    skip_timeout = false;
    data = new FormData();
    var fileSelect = document.getElementById('logo');
    var file = fileSelect.files[0];
    if (typeof file == 'undefined') {
        do_growl('Please select a file to upload.', 'danger');
        hide_busy();
        return false;
    }

    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL + "/logo",
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            hide_busy();
            if (response == 'error') {
                do_growl('Error saving image', 'danger');
                return false;
            }
            do_growl('Upload successful', 'success');
            $('#logo-preview').attr('src', response);
            var control = $("#logo");
            control.replaceWith(control = control.clone(true));

        }
    });
}



function add_to(block_id) {
    source = $('#options-list-swap-' + block_id);
    target = $('#options-list-final-' + block_id);
    if (source.val() < 1) {
        do_growl("Please select an answer to add", 'danger');
        return false;
    }
    already_included = false;
    $('#options-list-final-' + block_id + ' option').each(function() {
        if (this.value == source.val()) {
            do_growl("This option has already been included", 'danger');
            already_included = true;
            return false;
        }
    });
    if (already_included == true)
        return false;
    target.append(new Option($('#options-list-swap-' + block_id + ' option:selected').text(), source.val()));
}

function remove_from(block_id) {
    target = $('#options-list-final-' + block_id);
    if (target.val() < 1) {
        do_growl("Please select an answer to remove", 'danger');
        return false;
    }
    $('#options-list-final-' + block_id + ' option:checked').remove();
}

function equalize_cells() {
    $('.report-block td').each(function() {
        $(this).css('height', $(this).css('height'));
    });
    $('.report-block table').each(function() {
        $(this).css('height', '100%');
    });
}

function choose_program(){
    prog = $('#program_chooser').val();
    $.post(APP_URL+'/programs/choose/'+prog,function(data){
        window.location = 'http://' + window.location.hostname + window.location.pathname;
    });
}

function choose_program_id(id){
    show_busy()
    $.post(APP_URL+'/programs/choose/'+id,function(data){
        window.location = 'http://' + window.location.hostname + window.location.pathname;
    });
}

function create_add_to_list(){
    form = $('#create_form');
    url = form.attr('action');
    show_busy();
    $.post(url, form.serialize(), function(result){
        result = parse_json(result);
        if(result==false) return false;
        if(result.status=='success'){
            if($('#from_dash').length>0){
                window.location = 'lessons'; 
                return false;
            }
            $('.list-row').last().after(result.html);
            scroll_and_highlight(result.identifier,400);
            form[0].reset();
            $('.editable').editable();
        }
        else{
            
        }
        hide_busy();
        do_growl(result.text, result.status);
    });
    return false;
}

function scroll_and_highlight(element, speed){
    $('html body').animate({
        scrollTop: $(element).offset().top
    }, speed,  'linear', function() {
        $(element).effect("highlight", {}, 2000);
    });
}

function upload_user_image(id){
    skip_timeout = true;
    show_busy();
    file_id = 'file_'+id;
    file = document.getElementById(file_id);
    if(file.value==''){
        hide_busy();
        do_growl("Please select an image to upload.",'danger');
        return false;
    }
    
    if (!hasExtension(file_id, allowed_img)) {
        hide_busy();
        do_growl("This file type is not allowed.",'danger');
        return false;
    }
    file = file.files[0];
    data = new FormData();
    data.append('block', id);
    data.append("file", file);    
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL+"/courses/save_image",
        cache: false,
        contentType: false,
        processData: false,
                xhr: function(){
            $('#current_progress').remove();
            $('#'+file_id).before( $('#hidden_assets .progress').clone().attr('id','current_progress'));
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
              if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete*=100;
                percentComplete = parseInt(percentComplete);
                //Do something with upload progress
                $('#current_progress .indicator').attr('aria-valuenow', percentComplete);
                $('#current_progress .indicator').css('width', percentComplete+'%');
                $('#current_progress .indicator').html(percentComplete+'%');
              }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
              if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete*=100;
                percentComplete = parseInt(percentComplete);
                //Do something with upload progress
                $('#current_progress .indicator').attr('aria-valuenow', percentComplete);
                $('#current_progress .indicator').css('width', percentComplete+'%');
                $('#current_progress .indicator').html(percentComplete+'%');
                //Do something with download progress
              }
            }, false);
            return xhr;
          },
        success: function(response) {
            hide_busy();
            if(response.indexOf('--error--')!=-1){
                response = response.replace('--error--','');
                do_growl(response, 'danger');    
                $('#current_progress .indicator').addClass('progress-bar-danger');
                console.log('complete error');
                return false;
            }
            else{
                $('#current_progress').remove();
                $('.filename-holder-'+id).html('');
                $('.current_img_'+id).html("<img src='"+response+"' target='_blank' />");
                do_growl('Upload complete','success');
                console.log('complete success');
            }
            
        }
    });
}

function upload_user_file(id){
    skip_timeout = true;
    show_busy();
    file_id = 'file_'+id;
    file = document.getElementById(file_id);
    if(file.value==''){
        hide_busy();
        do_growl("Please select a file to upload.",'danger');
        return false;
    }
    
    if (!hasExtension(file_id, allowed )) {
        hide_busy();
        do_growl("This file type is not allowed.",'danger');
        return false;
    }
    file = file.files[0];
    data = new FormData();
    data.append('block', id);
    data.append("file", file);    
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL+"/courses/save_file",
        cache: false,
        contentType: false,
        processData: false,
                xhr: function(){
            $('#current_progress').remove();
            $('#'+file_id).before( $('#hidden_assets .progress').clone().attr('id','current_progress'));
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
              if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete*=100;
                percentComplete = parseInt(percentComplete);
                //Do something with upload progress
                $('#current_progress .indicator').attr('aria-valuenow', percentComplete);
                $('#current_progress .indicator').css('width', percentComplete+'%');
                $('#current_progress .indicator').html(percentComplete+'%');
              }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
              if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete*=100;
                percentComplete = parseInt(percentComplete);
                //Do something with upload progress
                $('#current_progress .indicator').attr('aria-valuenow', percentComplete);
                $('#current_progress .indicator').css('width', percentComplete+'%');
                $('#current_progress .indicator').html(percentComplete+'%');
                //Do something with download progress
              }
            }, false);
            return xhr;
          },
        success: function(response) {
            hide_busy();
            if(response.indexOf('--error--')!=-1){
                response = response.replace('--error--','');
                do_growl(response, 'danger');    
                $('#current_progress .indicator').addClass('progress-bar-danger');
                console.log('complete error');
                return false;
            }
            else{
                response = parse_json(response);
                str = "<a href='"+response.url+"'>[Download - "+response.size+"]</a>";
                $('#current_progress').remove();
                $('.filename-holder-'+id).html('');
                $('.uploaded_file_'+id).html(str);
                do_growl('Upload complete','success');
                console.log('complete success');
            }
            
        }
    });
}

function hasExtension(inputID, exts) {
    var fileName = document.getElementById(inputID).value.toLowerCase();
    return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
}

function upload_dialog(identifier){
    $(identifier).click();
}

function image_selected(id){
    file_id = 'file_'+id;
    file = document.getElementById(file_id).value.split('/');
    file = file[file.length-1];
    file = document.getElementById(file_id).value.split('\\');
    file = file[file.length-1];
    $('.filename-holder-'+id).html(file);
}

function do_update_form(e){
    form = e.target;
    console.log(form);
    $.ajax({
        url: $(form).attr('action'),
        type: $(form).attr('method'),
        data: $(form).serialize(),
        success: function(result) {
            result = parse_json(result);
            do_growl(result.text, result.status);
        },
        error: function(){
            do_growl('An Error Occurred - please refresh the page and try again', 'danger');
        }
    });
    return false;
}

function update_percentage(){
    if(typeof(updated_percentage)=='undefined') return;
    if(updated_percentage != $('.progress').attr('data-progress')){
         $('.progress').attr('data-progress', updated_percentage);
         $('.progress-bar-info span').html(updated_percentage);
         $('.bar').html(updated_percentage+'%');
         $('.bar').css('width',updated_percentage+'%');
    }
}

function toggle_box(identifier){
    $(identifier).slideToggle();
}

function show_div(btn){
    id = $(btn).attr('id')+'_div';
    $('.nodisplay').hide();
    $('#'+id).show();
}

function process_ajax_form(e){
    form = e.target;
    $(form).bootstrapValidator('validate');
    bv = $('#'+$(form).attr('id')).data('bootstrapValidator');
    if(!bv.isValid()) return false;
    bv.disableSubmitButtons(true);
    old_label = $(form).find('[data-submit="1"]').html();
    $(form).find('[data-submit="1"]').html('Please wait...');
    $(form).find('[data-submit="1"]').attr('disabled', 'true');
    $.post($(form).attr('action'), $(form).serialize(), function(result){
        $(form).find('[data-submit="1"]').removeAttr('disabled');
        result = parse_json(result);
        $(form).find('[data-submit="1"]').html(old_label);
        if(result.status=='success') bv.resetForm(true);
        if(typeof(result.callback)!='undefined') {
            window[result.callback](result);
            return false;
        }
        do_growl(result.text, result.status);
    });
    return false;
}


function show_codes(json){
    $('.ajax-codes').remove();
    
    $('#register_codes_div').append('<p class="ajax-codes">'+json.codes+'</p>');
}

function resettable(value, validator) {
    return true;    // just return true so that the resetform clears this field as well
}

function slideToggle(elem){
    enable_rte(2);
    $(elem).slideToggle();
}

function ajax_btn_update(element){
    element = $(element);
    if(element.length==0) return false;
    input = element.attr('data-ui-field');
    val = $(input).code();
    if(val=='' || typeof(val)=='undefined') val = $(input).val();
    if(val=='' || typeof(val)=='undefined') val = element.val();
    if(typeof(element.attr('data-method'))!='undefined' && element.attr('data-method')!='') type = element.attr('data-method');
    else type = 'POST';
    $.ajax({
       type: type,
       url: element.attr('data-url'),
       data: { pk: element.attr('data-pk'), name: element.attr('data-field'), value: val},
       success: function(){
           do_growl('Saved','success');
       },
       error: function(){
           do_growl('An error occurred', 'danger');
       }
    });
}

function toggle_remarks(){
    $('.remarks-container').toggle();
    if($('.remarks-container:visible').length==0){
        $('.remark-btn i').removeClass('glyphicon-resize-small');
        $('.remark-btn i').addClass('glyphicon-resize-full');
    }
    else{
        $('.remark-btn i').removeClass('glyphicon-resize-full');
        $('.remark-btn i').addClass('glyphicon-resize-small');
    }
}

function launch_program(url, id){
    
    $.post(url, {pk:id, name:'launched', value:1}, function(){
        $('#launch_options').remove();
        do_growl('Congrats!','success');
    });
}

function add_client_modal(new_modal, selected ){
    if(new_modal==1){
        bootbox.dialog({
          message: loader_gif,
          title: "Add Client"
        });
    }
    $('.bootbox-body').load(APP_URL+'/users/add_clients_ui', function(){
        if(typeof(selected)!='undefined'){
             $(selected).click();
        }
    });       
}


function link_sent(response){
    $('.bootbox-body').html(response.text);
    var client = new ZeroClipboard($(".copy-to"));
    $('#global-zeroclipboard-html-bridge').on('focusin', false);
}

function enable_copy_to_clipboard(){
     var client = new ZeroClipboard($(".copy-to"));
}


/// combobox
(function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
/// combobox    


var delay = (function(){
  var timer = 0;
  return function(callback, ms, params){
    clearTimeout (timer);
    timer = setTimeout(callback, ms, params);
  };
})();

function update_block_score(e){
    $target = $(e.target);
    block = $target.attr('data-block');
    min_v = $target.attr('data-min');
    max_v = $target.attr('data-max');
    val = $target.val();
    if($.trim(val)=='') return false;
    val = parseInt(val);
   
    $target.val(val);
    console.log( val );
    if(val < min_v || val > max_v || isNaN(val)){
        if(isNaN(val)) val = 0;
        val = val.toString();
        $target.val(val.substring(0, val.length-1));
        do_growl('Please enter a score between '+min_v+' and '+max_v+'.', 'danger');
    }
    score = 0;
    $('.block-score[data-block='+block+']').each(function(){
        score += 1*this.value;
    });
    $('.block-score-total[data-block="'+block+'"]').val(score);
}

function cancel_subscription(id, provider){
    bootbox.confirm("Are you sure you want to cancel this subscription?", function(result) {
          if(result==true){
            $.post(APP_URL+'/subscriptions',{id:id, provider:provider}, function(result){
                    result = parse_json(result);
                    do_growl(result.text, result.status);
                    if(result.status=='success'){
                        $('.row-'+id).remove();
                    }
                });
          }
    }); 
    $('[data-bb-handler=cancel]').removeClass('btn-default');
    $('[data-bb-handler=cancel]').addClass('btn-danger');
    
}

function paypal_check(){
    $.get('paypal_check',{},function(result){
        console.log(result);
        if(result==0) setTimeout(paypal_check, 3000);
        else{
            do_growl('Confirmation successful. Redirecting...','success');
            setTimeout(function(){
                window.location = APP_URL;
            }, 3000);
        }
    });
}

function new_item(e){
    url = $(e).attr('data-url');
    data = $(e).attr('data-data');
    append_to = $(e).attr('data-appendto');
    $.post(url, {data:data}, function(result){
        result = parse_json(result);
        if(result.status=='success'){
            if(typeof(result.text)!='undefined' && result.text!='') do_growl(result.text, result.status);
            $(append_to).append(result.html);
        }
        else{
            do_growl(result.text, result.status);
            if(typeof(result.error)!='undefined') console.log(result.error);
        }
    });
}

function change_alert_type(elem){
    $elem = $(elem);
    $indicator = $('.alert-type-'+$elem.attr('data-id'));
    if($elem.hasClass('glyphicon-phone')){
        $indicator.removeClass('glyphicon-envelope');
        $indicator.addClass('glyphicon-phone');
        $indicator.attr('title','Text Alert. Click to change alert type');
        $indicator.attr('data-original-title','Text Alert. Click to change alert type');
        type = 'text';
    }
    else{
        $indicator.removeClass('glyphicon-phone');
        $indicator.addClass('glyphicon-envelope');
        $indicator.attr('title','Email Alert. Click to change alert type');
        $indicator.attr('data-original-title','Email Alert. Click to change alert type');
        type = 'email';
    }
    alert_type = 'email';
    
    element = $('<input />').attr({'data-id':$elem.attr('data-id') , 'data-method':'PUT','data-field':'delivery_type','value':alert_type, 
        'data-url': $elem.attr('data-url')});
    ajax_btn_update(element);
    $('.do-tooltip').popover('hide');
    $('.do-tooltip').tooltip('hide');
}

function set_user_timezone(){
    var tz = jstz.determine(); // Determines the time zone of the browser client
    $('#tz').val(tz.name());
}

function add_two_column_row(e){
    block = $(e.target).attr('data-block');
    row = $(e.target).attr('data-row');
    row++;
    if($('.two-column-'+block+'[data-row='+row+']').length==0){
        new_row = '<tr><td><input data-block="'+block+'" data-row="'+row+'" type="text" class="form-control two-column two-column-'+block+'" ' +
                  'name="two-column['+block+']['+row+'][1]" /></td> ' +
                  '<td><input data-block="'+block+'" data-row="'+row+'" type="text" class="form-control two-column two-column-'+block+'" '+
                  'name="two-column['+block+']['+row+'][2]" /></td></tr>';
        
        $('.two-column-table-'+block+' tbody').append(new_row);
    }
}


function link_remote_change_element(target){
    $target = $(target);
    $.post($target.attr('data-url'), function(result){
        result = parse_json(result);
        if(result.status=='success'){
            remove = $target.attr('data-remove-element');
            $(remove).remove();
            add = $target.attr('data-add-element');
            $target.prepend(add);
            do_growl('Saved','success');
        }
        else{
            do_growl('An error occurred','danger');
            console.log(result);
        }
    });
    
}

function enable_autosave_lesson(){
    if($('#lesson_form').length==1){
        lesson_name = lesson_name.toString();
        // see if we need to expire the data
        storage = JSON.parse(localStorage.getItem(lesson_name));
        name = lesson_name+'-set-date';
        if( localStorage.getItem(name) !== null ){
            current = new Date().getTime();
            date_set = localStorage.getItem(name);
            dif = (current - date_set)/1000/60;
            // expire after 30 minutes
            if(dif >= 30){
                localStorage.removeItem(name);
                localStorage.removeItem(lesson_name);
            }
        }
        
        $("#lesson_form").rememberState({
            objName:lesson_name,
            noticeDialog: $("<div class='rememberStateDiv' />").html("<p>"+autosave_question+"</p> <button class='rememberStateYes btn btn-success' href='#'>"+autosave_yes_button_label+"</button>  <button class='rememberStateNo btn btn-danger' href='#'>"+autosave_no_button_label+"</button>"),
            noticeConfirmSelector: "button.rememberStateYes",
            noticeCancelSelector: "button.rememberStateNo"
        });
        t = new Date().getTime();
        name = lesson_name+'-set-date';
        if( localStorage.getItem(name) === null ){
            localStorage.setItem(name, t);
        }
    }
}
