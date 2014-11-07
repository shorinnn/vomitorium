$(document).ajaxComplete(function(){
    $('.accordion').off('shown.bs.collapse');
    $('.accordion').on('shown.bs.collapse', function () {
      load_inbox_convo();
    });
});
$(function(){
    $('body').on('change','.convo-filter', filter_conversations);
    $('body').on('click','.earlier-convo', load_earlier_convo);
    $('.accordion').on('shown.bs.collapse', function () {
      load_inbox_convo();
    });
});

function filter_conversations() {
    $.get(APP_URL + '/inbox', {filter: $('.convo-filter').val()}, function(result) {
        $('#ajax-content').html(result);
    });
}

function search_inbox() {
    var autoload = 0;
    //$('.search-results').html('<center><img src="' + APP_URL + '/assets/img/ajax-loader_1.gif" /><h4 style="line-height:200px">Searching...</h4></center>');
    $('.searching-placeholder').css('visibility','visible');
    term = $('.inbox-search').val();
    if (typeof (search_term) != 'undefined') {
        term = search_term;
        delete search_term;
        autoload = 1;
    }
    if(term==''){
        $('.search-results').html('');
        $('.search-results').removeClass('results-found');
        $('.inbox-area').show();
        $('.searching-placeholder').css('visibility','hidden');
        return false;
    }
    $.post(APP_URL + '/pm_search', {term: term}, function(result) {
        $('.searching-placeholder').css('visibility','hidden');
        $('.inbox-area').hide();
        $('.search-results').addClass('results-found');
        if ($.trim(strip_tags(result)) == ''){
            $('.search-results').html('<p class="alert alert-info">Sorry, couldn\'t find what you are looking for.</p>');
        }
        else {
            
            $('.search-results').html("<p class='res-label'>Here's what we found:</p>" + result);
            if (autoload == 1) {
                $('.search-results').html("<p class='res-label'>Here's your message:</p>" + result);
                $('.search-results').find('[data-toggle=collapse]').first().effect("highlight", {}, 1000);
                $('.search-results').find('[data-toggle=collapse]').first().trigger('click');
            }
        }
    });
}

function load_earlier_convo(e) {
    id = $(e.target).attr('data-convo');
    skip = $(e.target).attr('data-skip');
    url = $(e.target).attr('data-url');
    $(e.target).html('Loading...');
    $.post(url, {skip: skip}, function(response) {
        console.log(response);
        if ($.trim(strip_tags(response)) == '') {
            $(e.target).html('No additional messages available');
            $(e.target).addClass('disabled-btn');
            $(e.target).removeClass('earlier-convo');
            return false;
        }
        $(e.target).parent().find('.convo-box').first().before(response);
        $(e.target).attr('data-skip', 1 * skip + 2 * 1);
        $(e.target).html('Load earlier messages');
    });
}

function load_inbox_convo(e) {
    populated = true;
    accordion = null;
    $('.collapse.in').each(function() {
        if ($.trim(strip_tags($(this).find('.panel-body').html())) == 'Loading...') {
            populated = false;
            accordion = $(this).parent().parent().attr('id');
        }
    });
    if (populated == false) {
        id = $('#' + accordion).find('.collapse.in').attr('data-convo');
        $('#' + accordion).find('.collapse.in').prev('.panel-heading').find('.panel-title a').removeAttr('style');
        $('#' + accordion).find('.collapse.in').find('.panel-body').load(APP_URL + '/load_convo/' + id);
    }
}

function pm_callback(json) {
    do_growl(json.text, json.status);
    $('#to').val(0);
    $('.custom-combobox-input').val('');
    $('#message').code('');
}

function delete_attachment(e) {
    e.preventDefault();
    $btn = $(e.target);
    id = $btn.attr('data-id');
    current_attachments = parse_json($('#attachments').val());
    for (i = 0; i < current_attachments.length; ++i) {
        if (current_attachments[i] == id) {
            current_attachments.remove(i);
            $('#attachments').val(JSON.stringify(current_attachments));
        }
    }
    current_attachments = parse_json($('#comment_attachments').val());
    for (i = 0; i < current_attachments.length; ++i) {
        if (current_attachments[i] == id) {
            current_attachments.remove(i);
            $('#comment_attachments').val(JSON.stringify(current_attachments));
        }
    }
    $.post(APP_URL + '/courses/delete_attachment', {id: id}, function() {
        $('.alert-dismissable [data-id=' + id + ']').remove();
    });
}

function upload_attachment(e) {
    $('[data-attach-failed=1]').remove();
    input = $(e.target).attr('id');
    rte = $(e.target).attr('data-rte');
    file = document.getElementById(input);
    if (file.value == '') {
        hide_busy();
        do_growl("Please select a file to attach.", 'danger');
        return false;
    }

    if (!hasExtension(input, allowed)) {
        hide_busy();
        do_growl("This file type is not allowed.", 'danger');
        return false;
    }

    if (input == 'attachment') {
        if ($('.alert-dismissible').length == 0) {
            $(rte).next('.note-editor').after('<div class="alert-dismissible message-col-alert" role="alert"><button type="button" class="close close-2" data-dismiss="alert"></button>     <p></p></div>');
        }
        else {
            $(rte).parent().find('.alert-dismissible').last().after('<div class="alert-dismissible message-col-alert" role="alert"><button type="button" class="close close-2" data-dismiss="alert"></button>     <p></p></div>');
        }
        $box = $(rte).parent().find('.alert-dismissible').last();
    }
    else {
        if ($('#reply_form .alert-dismissible').length == 0) {
            $('#reply_form .note-editor').last().after('<div class="alert-dismissible message-col-alert" role="alert"><button type="button" class="close close-2" data-dismiss="alert"></button>     <p></p></div>');
        }
        else {
            $('#reply_form .alert-dismissible').last().after('<div class="alert-dismissible message-col-alert" role="alert"><button type="button" class="close close-2" data-dismiss="alert"></button>     <p></p></div>');
        }
        $box = $('#reply_form .alert-dismissible').last();
    }


    file = file.files[0];
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL + "/courses/attach",
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
            $('#current_progress').remove();
            $box.find('p').append($('#hidden_assets .progress').clone().attr('id', 'current_progress'));
            $box.find('#current_progress').removeAttr('style');
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    percentComplete *= 100;
                    percentComplete = parseInt(percentComplete);
                    //Do something with upload progress
                    $('#current_progress .indicator').attr('aria-valuenow', percentComplete);
                    $('#current_progress .indicator').css('width', percentComplete + '%');
                    $('#current_progress .indicator').html(percentComplete + '%');
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    percentComplete *= 100;
                    percentComplete = parseInt(percentComplete);
                    //Do something with upload progress
                    $('#current_progress .indicator').attr('aria-valuenow', percentComplete);
                    $('#current_progress .indicator').css('width', percentComplete + '%');
                    $('#current_progress .indicator').html(percentComplete + '%');
                    //Do something with download progress
                }
            }, false);
            return xhr;
        },
        success: function(response) {
            response = parse_json(response);
            if (response.status == 'success') {
                $('#current_progress').remove();
                $box.find('p').html("<span><a href='" + response.url + "' target='_blank'>" + response.orig_name + "</a></span>");
                $box.find('button').attr('data-id', response.id);
                $box.find('button').click(delete_attachment);
                $box.attr('data-id', response.id);
                attachments_ui = input + 's';
                current_attachments = parse_json($('#' + attachments_ui).val());
                current_attachments.push(response.id);
                $('#' + attachments_ui).val(JSON.stringify(current_attachments));
            }
            else {
                $box.attr('data-attach-failed', 1);
                do_growl(response.text, 'danger');
                $('#current_progress .indicator').addClass('progress-bar-danger');
            }
            $('#' + input).val('');
        }
    });
}

function attach(e) {
    e.preventDefault();
    input = $(e.target).attr('data-input');
    $('#' + input).attr('data-rte', $(e.target).attr('data-rte'));
    $('#' + input).click();

}

function cancel_edit_remark(evt, id) {
    evt.preventDefault();
    box = $('.remark-box-' + id);
    box.find('.edit-remark-ui').remove();
    box.find('.text').show();
}

function do_edit_remark(id) {
    box = $('.remark-box-' + id);
    str = box.find('.summernote_editor').code();
    btn = box.find('.message-send');
    loading_button(btn);
    $.post(APP_URL + '/edit_remark', {id: id, text: str}, function() {
        loading_button(btn, true);
        box.find('.text').html(str);
        cancel_edit_remark(event, id);
        box.find('.text').effect("highlight", {}, 2000);
        box.find('.remark-time').html('1 second ago');
    });
}

function edit_comment(e, block, id) {
    e.preventDefault();
    if ($('.edit-comment-txt').length > 0)
        return false;
    $('.reply-bar-' + block).hide();
    cnt = $('#comment-' + id).find('.text');
    cnt.hide();
    $('#comment-' + id).find('.attachment').after('<div class="edit-comment-ui"><textarea class="summernote_editor edit-comment-txt">' + cnt.html() + '</textarea><button type="button" class="btn btn-default2 message-send" onclick="do_comment_edit(' + block + ',' + id + ')">Edit</button></div>');
    $(cnt).parent().find('.btn').after('<ul class="list-unstyled option-box-2"><li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" onclick="cancel_edit_comment(event,' + id + ')" class="do-tooltip icon-3"></a></li></ul>')
    enable_rte(3);
    $('#comment-' + id).find('.note-editable').focus();
}

function cancel_edit_comment(evt, id) {
    $('.tooltip').remove();
    evt.preventDefault();
    box = $('#comment-' + id);
    box.find('.edit-comment-ui').remove();
    box.find('.text').show();
    box.parent().parent().next('.reply_area2').find('.reply-bar').show();
}
var reply_in_block = 0;
function comment_reply(block_id, evt) {
    if(reply_in_block!=0 && reply_in_block!=block_id){
        $('.reply-comments-' + reply_in_block).show();
        $('.reply-bar-' + reply_in_block).show();
        $('#reply_form').remove();
    }
    if ($('#reply_form').length > 0) {
        if(typeof(evt)!='undefined'){
            discard(evt);
            evt.preventDefault();
        }
        $('.do-tooltip').tooltip('hide');
        $('.reply-comments-' + block_id).html('Reply');
        $('#reply_form').slideToggle('fast', function(){
            $('#reply_form').remove();
        });
        $('.reply-comments-' + block_id).show();
        $('.reply-bar-' + block_id).show();
        return;
    }
    $('#reply_form').remove();
    $('.reply-bar-' + block_id).hide();
    $("#comment_attachments").val('[]');
    str = '<div id="reply_form" style="display:none"><textarea  id="comment_reply" class="white-textarea summernote_editor"></textarea><button type="button" class="btn btn-default2 message-send"  onclick="do_reply(' + block_id + ','+evt+')">Send</button>';
    str += '<ul class="list-unstyled option-box-2"><li><a href="#" data-toggle="tooltip" title="" data-input="comment_attachment" data-original-title="Attach" class="do-tooltip icon-2" onclick="attach(event)"></a></li><li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" data-target="#comment_reply" onclick="comment_reply('+block_id+', event)" class="do-tooltip icon-3"></a></li></ul>';
    $('#comments-' + block_id).append(str);
    enable_rte();
    $('#reply_form').slideToggle('fast');
    reply_in_block = block_id;
}

function edit_remark(e) {
    e.preventDefault();
    if ($('.edit-remark-txt').length > 0)
        return false;
    id = $(e.target).attr('data-id');
    cnt = $(e.target).parent().parent().parent().find('.text');
    cnt.hide();
    $(e.target).parent().parent().parent().find('.attachment').after('<div class="edit-remark-ui"><textarea class="summernote_editor edit-remark-txt">' + cnt.html() + '</textarea><button type="button" class="btn btn-default2 message-send" onclick="do_edit_remark(' + id + ')">Edit</button></div>');
    $(cnt).parent().find('.btn').after('<ul class="list-unstyled option-box-2"><li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" onclick="cancel_edit_remark(event,' + id + ')" class="do-tooltip icon-3"></a></li></ul>')
    enable_rte(3);
}

function discard(e) {
    // todo better discard for multi-instances
    if(typeof(e)=='undefined') return false;
    e.preventDefault();
    rte = $(e.target).attr('data-target');
    $('.summernote_editor').code('');
    $('.close-2').click();
}

function cancel_remark_reply() {
    $('#remark_reply_form').remove();
    $('.remark-reply-btn').show();
}

function do_remark_reply(event, lesson) {
    $rte = $($(event.target).attr('data-rte'));
    $container = $($(event.target).attr('data-container'));

    reply_txt = $rte.code();
    if ($.trim(strip_tags(reply_txt)) == '') {
        do_growl("Cannot post empty reply", 'danger');
        return false;
    }
    show_busy();
    $('#remark_reply_form').remove();
    $.post(APP_URL + '/remark_reply', {lesson: lesson, reply_txt: reply_txt, attachments: $('#attachments').val()}, function(result) {
        if (result == 'error') {
            do_growl('An error was encountered. Please refresh the page and try again', 'danger');
            hide_busy();
            return false;
        }
        result = parse_json(result);
        //$('.remark-box').last().after(result.html);
        $container.append(result.html);
        cancel_remark_reply();
        discard(event);
        hide_busy();
    });
}

function remark_reply(lesson) {
    $('#remark_reply_form').remove();
    str = '<div id="remark_reply_form" class="white-textarea-container comment-textarea"><textarea  id="remark_reply" class="white-textarea summernote_editor"></textarea><button type="button" class="buttons short-buttons cyan-button" onclick="do_remark_reply(' + lesson + ')">SUBMIT</button><button type="button" class="buttons short-buttons cyan-button cancel-button" onclick="cancel_remark_reply()">CANCEL</button></div>';
    $('.remark-reply-btn').before(str);
    $('#remark_reply').focus();
    $('.remark-reply-btn').hide();
    enable_rte(1);
}

function show_coach_remarks() {
    $('html body').animate({
        scrollTop: 0
    }, 1000);
    $('#coach_remarks').slideToggle();
    $('#coach_remarks').effect("highlight", {}, 2000);
}

function cancel_coach_remarks() {
    $('#coach_remarks').slideToggle();
}
function hide_remark_warning() {
    $('#remark-warning').fadeOut(1000);
}
function post_coach_remarks(event, url) {
    $target = $(event.target);
    $rte = $($target.attr('data-rte'));
    $container = $($target.attr('data-container'));
    if (strip_tags($rte.code()) == '') {
        do_growl('Cannot post empty message!', 'danger');
        $rte.focus();
        return false;
    }
    loading_button($target);
    remark = $rte.code();
    $.post(url, {user: $('#remark_user').val(),
        lesson: $('#remark_lesson').val(),
        attachments: $('#attachments').val(),
        remark: remark}, function(result) {
        loading_button($target, true);
        result = parse_json(result);
        $('#remark-' + result.id).remove();
        $('.conversations-title').removeClass('hidden');
        $container.append(result.html);
        do_growl('Remarks successfully posted.', 'success');
        $rte.code('');
        discard(event);
    });
}

function do_comment_edit(block, id) {
    if (strip_tags($('.edit-comment-txt').code()) == '') {
        do_growl('Cannot post empty comment', 'danger');
        return false;
    }
    val = $('.edit-comment-txt').code();
    btn = $('.edit-comment-ui').find('.message-send');
    loading_button(btn);
    $.post('/edit_reply', {id: id, txt: val}, function(result) {
        loading_button(btn, true);
        box = $('#comment-' + id);
        box.find('.text').html(val);
        cancel_edit_comment(event, id);
        box.find('.text').effect("highlight", {}, 2000);
    });
}

function loading_button(btn, is_end){
    if(typeof(is_end)=='undefined'){
        btn.addClass('disabled-btn');
        btn.attr('data-old-label', btn.html());
        btn.html('Please wait...');
    }
    else{
        btn.removeClass('disabled-btn');
        btn.html(btn.attr('data-old-label'));
        btn.removeAttr('data-old-label');
    }
}

function show_reply_form(block_id) {
    if ($('#reply_form').length > 0) {
        $('.reply-comments-' + block_id).html('Reply');
        $('#reply_form').remove();
        $('.reply-comments-' + block_id).show();
        return;
    }
    $('#reply_form').remove();
    str = '<div id="reply_form" class="white-textarea-container comment-textarea"><textarea  id="reply" class="white-textarea"></textarea><button type="button" class="buttons short-buttons cyan-button" onclick="do_reply(' + block_id + ')">SUBMIT</button><button type="button" class="buttons short-buttons cyan-button cancel-button" onclick="show_reply_form(' + block_id + ')">CANCEL</button></div>';
    $('#comments-' + block_id).append(str);
    $('#reply').focus();
    $('.reply-comments-' + block_id).html('Cancel');
    $('.reply-comments-' + block_id).hide();
}

function cancel_reply() {
    $('#reply_form').remove();
}

function do_reply(block_id) {
    reply_txt = $('#comment_reply').code();
    if ($.trim(strip_tags(reply_txt)) == '') {
        do_growl("Cannot post empty comment", 'danger');
        return false;
    }
    btn = $('#reply_form').find('.message-send');
    loading_button(btn);
    uid = $('#remark_user').val();
    $.post(APP_URL + '/reply', {block_id: block_id, reply_txt: reply_txt, uid: uid, attachments: $('#comment_attachments').val()}, function(result) {
        $('#reply_form').remove();
        result = parse_json(result);
        loading_button(btn, true);
        if (result.status == 'error') {
            console.log(result.text);
            do_growl('An error was encountered. Please refresh the page and try again', 'danger');
            hide_busy();
            return false;
        }
        if (result.marked_attended == 1) {
            skip_timeout = true;
            mark_submission_attended(block_id);
            skip_timeout = false;
        }

        $('#comments-' + block_id).append(result.text);
        $('.reply-comments-' + block_id).attr('onclick', 'edit_comment(event, ' + block_id + ',' + result.id + ')');
        $('.reply-comments-' + block_id).show();
        $('.reply-comments-' + block_id).html('Edit Comment');
        hide_busy();
    });
}

function mark_remark_read(message) {
    show_busy();
    $.post(APP_URL + '/mark_remark_read', {message: message}, function(result) {
        hide_busy();
        $('#mark-remark-read-' + message).parent().removeClass('unattended_item');
        $('#mark-remark-read-' + message).remove();

    });
}

function mark_remark_attended(message) {
    show_busy();
    $.post(APP_URL + '/mark_remark_attended', {message: message}, function(result) {
        hide_busy();
        $('#mark-remark-read-' + message).parent().removeClass('unattended_item');
        $('#mark-remark-read-' + message).remove();
    });
}

function mark_attended(message, block_id) {
    show_busy();
    $.post(APP_URL + '/mark_attended', {message: message, block_id: block_id}, function(result) {
        hide_busy();
        $('#mark-read-' + message).hide();
    });
}

function mark_unattended(message, block_id) {
    show_busy();
    $.post(APP_URL + '/mark_unattended', {message: message, block_id: block_id}, function(result) {
        hide_busy();
        $('#mark-read-' + message).html('<i class="glyphicon glyphicon-ok"></i> Mark as attended');
        $('#mark-read-' + message).attr('onclick', 'mark_attended(' + message + ',' + block_id + ')');
        $('#next_item_btn').show();
        $('#comment-' + message).addClass('purple-border');
    });
}

function mark_read(message, block_id, block) {
    show_busy();
    $.post(APP_URL + '/mark_read', {message: message, block_id: block_id}, function(result) {
        hide_busy();
        $('#mark-read-' + message).parent().removeClass('new_comment');
        $('#mark-read-' + message).parent().removeClass('purple-border');
        $('#mark-read-' + message).remove();
        $('.unread-' + block).remove();
    });
}

function load_lesson_comments(lesson_id, skip) {
    show_busy();
    $.get(APP_URL + '/load_lesson_comments', {lesson_id: lesson_id, skip: skip}, function(result) {
        result = parse_json(result);
        remaining = result.remaining;
        result = result.comments;
        $('.lesson-comments').prepend(result);
        if (result != '' && remaining > 0) {
            skip += 2;
            $('.load-lesson-comments').attr('onclick', 'load_lesson_comments(' + lesson_id + ',' + skip + ')');
        }
        else {
            $('.load-lesson-comments').html('No additional messages available');
            $('.load-lesson-comments').removeAttr('onclick');
        }
        hide_busy();
    });
}

function load_messages(answer_id, skip) {
    show_busy();
    $.get(APP_URL + '/load_messages', {answer_id: answer_id, skip: skip}, function(result) {
        result = parse_json(result);
        remaining = result.remaining;
        result = result.comments;
        $('#comments-' + answer_id).prepend(result);
        $('#comments-' + answer_id).prepend($('.load-comments-' + answer_id));
        $('#comments-' + answer_id).append($('#reply_form'));
        if (result != '' && remaining > 0) {
            skip += 2;
            $('#load-comments-' + answer_id).attr('onclick', 'load_messages(' + answer_id + ',' + skip + ')');
        }
        else {
            $('#load-comments-' + answer_id).html('No additional messages available');
            $('#load-comments-' + answer_id).removeAttr('onclick');
            $('#load-comments-' + answer_id).addClass('disabled');
            $('#load-comments-' + answer_id).attr('disabled', true);
        }
        hide_busy();
    });
}

function force_edit(target){
    $(target).find('.edit-remark-btn').first().click();
}