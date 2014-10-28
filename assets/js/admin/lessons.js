
$(function(){
    
    $(window).bind('keydown', function(event) {
        if (event.ctrlKey || event.metaKey) {
            switch (String.fromCharCode(event.which).toLowerCase()) {
            case 's':
                event.preventDefault();
                save_all();
                return false;
                break;
            }
        }
    });
    $('body').on('change keyup','.block input, .block textarea, .block select', blocks_changed);
    //ready
    release_options();
    $( "#date" ).datepicker({
      showAnim: 'drop',
      showOn: "both",
      buttonImageOnly: false,
      buttonText: '<i class="glyphicon glyphicon-calendar"></i>'
    });
    //
    $('.mc-textarea').elastic();
    window.selection = null;
    $('body').on('click','.add_category',add_category);
    
    $('body').on('click','.note-editable td', function(e) {
        window.selection = document.getSelection();
      });
      
    $('.summernote_editor').summernote({
       
        minHeight: 140,
        toolbar: [
        ['style', ['style','bold', 'italic', 'underline','clear']],
        ['para', ['paragraph']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['lists',['ul', 'ol']],
        ['insert', ['link','picture']],
        ['misc', ['codeview']]
  ],
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0],editor,welEditable);
        },
        onfocus: function(e) {
            window.selection = document.getSelection();
            console.log('changed selection');
          },
       onkeyup: function(e) {
            window.selection = document.getSelection();
            console.log('changed selection');
            blocks_changed(e);
          }
    });
    $('[data-original-title=Style]').html('Styles <span class="caret"></span>');
    
    if($.trim($("#publish_btn").html())=='Unpublish'){
        $('#public_view_link').show();
    }
    
    $('#published').on('save', function(e, params) {
        if(params.newValue=='0') $('#public_view_link').hide();
        else $('#public_view_link').show();
    });
});

function release_options(){
    $('#release_date, #release_days').hide();
    if($('#release').val()=='on_date') $("#release_date").show();
    if($('#release').val()=='after') $("#release_days").show();
    update_release_data( $('#release') );
}

function update_release_data(element){
    element = $(element);
    if(element.length==0) return false;
    val = element.val();
    if(element.attr('id')=='email_release') val = $('#email_release:checked').length;
    if(element.attr('id')=='save_email') val = $('#edit_email_rte-content').code();
    $.post(element.attr('data-url'),{ 
            pk: element.attr('data-pk'), 
            name: element.attr('data-field'), 
            value: val
        }, function(){
            if(element.parent().attr('data-isdiv')!=1){
                if(element.parent().attr('data-isrte')!=1) element.parent().prev().append("<i class='glyphicon glyphicon-floppy-saved pull-right'></i>");
                else element.parent().find('p').last().append("<i class='glyphicon glyphicon-floppy-saved'></i>");
            }
            else element.parent().parent().prev().append("<i class='glyphicon glyphicon-floppy-saved pull-right'></i>");
            $('.glyphicon-floppy-saved').fadeOut(1000);
        }
    );
}

$.fn.editable.defaults.success = function(result) {
    try {
        result = jQuery.parseJSON(result);
    }
    catch (e) {
    }
    if (result.text == 'chapter_id') {
        if ( typeof is_lesson_editor !== "undefined" && is_lesson_editor) {
            return true;
        }
        do_growl("Lesson successfully moved. <a href='"+result.edit_url+"'>Edit Lesson</a>", 'success');
        $('.list-row-' + result.pk).remove();
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
    if(result.text=='title'){
        $('#slug').html(result.slug);
        $('#permalink-tooltip').attr('data-original-title','Lesson URL will be '+result.url);
    }
    if (result.text == 'permalink') {
       // $('#permalink-tooltip').attr('title','Lesson URL will be '+result.url);
        $('#permalink-tooltip').attr('data-original-title','Lesson URL will be '+result.url);
    }
};

function lesson_editor() {
    bootbox.alert("UNDER DEVELOPMENT - This will work tommorrow");
}

function ellipsis(str, limit){
    if(str.length > limit){
        str = str.substring(0,limit-3);
        str += '...';
    }
    return str;
}
function toggle_edit_email(){
    $('#edit_email_rte').slideToggle();
}

function do_add_block(url, type, pos){
    $('.adding .box').prepend('<div class="disabler"></div>');
    $.post(url,{type:type, pos:pos},function(response){
        quick_end_new_page_element();
        response = parse_json(response);
        if(response.status=='danger'){
            do_growl(response.text, response.status);
            return false;
        }
        if(pos=='z'){
            //$('#blocks_list').append(response.html_string);
            $('.initial_area').after(response.html_string);
        }
        else{
            var i = 1;
            $('.block').each(function(){
                if(i == pos-1){
                    $(this).after(response.html_string);
                    return false;
                }
                ++i;
            });
        }
        $('#'+response.id).addClass('pop-in');
        $('html, body').animate({
            scrollTop: $('#'+response.id).offset().top - 70
        }, 1000);
        $('#'+response.id).effect("highlight", {}, 3000);
        $('.add-block-btn').show();
        $('#'+response.id+' .summernote_editor').summernote({ 
            minHeight: 140,
            toolbar: [
                ['style', ['style','bold', 'italic', 'underline','clear']],
                ['para', ['paragraph']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['lists',['ul', 'ol']],
                ['insert', ['link','picture']],
                ['misc', ['codeview']]
  ],
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0],editor,welEditable);
            },
            onfocus: function(e) {
                window.selection = document.getSelection();
                console.log('changed selection');
              }
              ,
            onkeyup: function(e) {
                window.selection = document.getSelection();
                console.log('changed selection');
                blocks_changed(e)
              }
        });
        $('[data-original-title=Style]').html('Styles <span class="caret"></span>');

    });
}

moving_block = 0;
move_id = 0;
function move_block_at_pos(id){
    if(moving_block==0){
        $('.add_block_area').addClass('move_block');
        moving_block = 1;
        move_id = id;        
        $('body').on('click','.add_block_area', do_move_block);
    }
    else{
        $('.add_block_area').removeClass('move_block');
        moving_block = 0;
        move_id = 0;
        $('body').unbind('click',do_move_block);
    }
}

function do_move_block(e){
    show_busy();
    $.post( APP_URL+"/lessons/blocks/move_block_to_pos", {move:move_id, target:$(e.target).attr('data-block-id')}, function(result){
        console.log(result);
        hide_busy();
        target_block = $(e.target).attr('data-block-id');
        target_block *= 1;
        if(isNaN(target_block)) target_block = 0;
        if(target_block==0){
            $('.initial_area').after( $('#block-'+move_id) );
        }
        else{
            $('#block-'+target_block).after( $('#block-'+move_id) );
        }
        move_block_at_pos(move_id);
    });
}

function move_block(dir, id, url){
    show_busy();
    $.post(url,{}, function(response){
        hide_busy();
        if(response!=''){
            do_growl(response,'danger');
            return false;
        }
        if(dir=='down'){
            var next_block = $('#'+id).next();
            $("#"+id).before(next_block);
        }
        else{
            var next_block = $('#'+id).prev();
            $("#"+id).after(next_block);
        }
    });
    
}


function save_answer_block(block_id, url){
    show_busy();
    answer_id =  $('#block-answer-'+block_id).val();
    skill_type = $('#block-answer-'+block_id+' option:selected').data('skill-type');
    console.log(answer_id + ' '+skill_type);
    var in_section = $('#in_section-'+block_id).val();
    category_id =  $('#category-'+block_id).val();
    $.post(url,{answer_id:answer_id, skill_type:skill_type, in_section:in_section, category_id:category_id}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}

function save_category_block(block_id, url){
    show_busy();
    title =  $('#block-title-'+block_id).val();
    category_id =  $('#block-cat-'+block_id).val();
    var in_section = $('#in_section-'+block_id).val();
    
    $.post(url,{title:title, in_section:in_section, category_id:category_id}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}

function save_sortable_block(block_id, url){
    show_busy();
    skill_type = $('#options-list-'+block_id).val();
    title =  $('#block-title-'+block_id).val();
    subtitle =  $('#block-subtitle-'+block_id).val();
    $('#block-title-span-'+block_id).html(title);
    var in_section = $('#in_section-'+block_id).val();
    category_id =  $('#category-'+block_id).val();
    var minimum_choices = $('#block-answer-min-skill-'+block_id).val();
    var maximum_choices = $('#block-answer-max-skill-'+block_id).val();
    var top_text = $('#block-answer-scale-max-text'+block_id).val();
    var bottom_text = $('#block-answer-scale-min-text'+block_id).val();
    var choices = new Array();
    $('#options-list-final-'+block_id+' option').each(function(){
        var c = {block_id:this.value, label:this.text};
        choices.push(c);
    });
    choices = JSON.stringify(choices);
    $.post(url,{skill_type:skill_type, in_section:in_section, category_id:category_id,
        minimum_choices:minimum_choices, maximum_choices:maximum_choices, title:title, subtitle:subtitle, 
    scale_max_text: top_text, scale_min_text:bottom_text, choices:choices}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}

function save_top_skill_block(block_id, url){
    show_busy();
    top_skill_type =  $('#block-top-skill-type-'+block_id).val();
    top_skill_count = $('#block-top-skill-count-'+block_id).val();
    var in_section = $('#in_section-'+block_id).val();
    category_id =  $('#category-'+block_id).val();
    subtitle =  $('#block-subtitle-'+block_id).val();
    $.post(url,{top_skill_type:top_skill_type, top_skill_count:top_skill_count, in_section:in_section, 
        category_id:category_id, subtitle:subtitle}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}

function save_score_block(block_id, url){
    show_busy();
    title = $("#block-title-"+block_id).val();
    subtitle = $("#block-subtitle-"+block_id).val();
    var in_section = $('#in_section-'+block_id).val();
    category_id =  $('#category-'+block_id).val();
    var entries = new Array();
    $('#scale-options-holder-'+block_id+' .option-input').each(function(){
        entries.push(this.value);
    });
    entries = JSON.stringify(entries);
    min_score= $("#scale-min-"+block_id).val();
    max_score= $("#scale-max-"+block_id).val();
    $.post(url,{title:title, subtitle:subtitle, in_section:in_section, 
        category_id:category_id, choices:entries, scale_min:min_score, scale_max:max_score}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}

function save_dynamic_block(block_id, url){
    cats = new Array();
    $('.dynamic-category-selected-'+block_id+':checked').each(function(){
        cats.push($(this).val());
    });
    cats = JSON.stringify(cats);
    show_busy();
    
    var in_section = $('#in_section-'+block_id).val();
    
    $.post(url,{in_section:in_section, subtitle:cats}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}

function save_text_block(block_id, url){
    show_busy();
    content = $('#block-editor-'+block_id).val();
    if(content=='')  content =  $('#block-editor-'+block_id).code();
    title =  $('#block-title-'+block_id).val();
    $('#block-title-span-'+block_id).html(title);
    var in_section = $('#in_section-'+block_id).val();
    category_id =  $('#category-'+block_id).val();
    
    $.post(url,{text:content, title:title, in_section:in_section, category_id:category_id}, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            if(!no_growl) do_growl(result.text, result.status); 
    });
}
first_upload = 0;
function save_first_file_block(block_id, url){
    $('#block-'+block_id+' .first').hide();
    $('#block-'+block_id+' .advanced').show();
    first_upload = 1;
    save_file_block(block_id, url);
    first_upload = 0;
}

function save_file_block(block_id, url){
    skip_timeout = true;
    show_busy();
    if(first_upload==1) file_id = 'first-block-editor-'+block_id;
    else file_id = 'block-editor-'+block_id;
    file = document.getElementById(file_id);
    no_file = 0;
    if(file.value==''){
        hide_busy();
        //do_growl("Please select a file to upload.",'danger');
        do_growl("No file has been specified. Updating title only.",'info');
        //return false;
        no_file = 1;
    }
    
    if (no_file==0 && !hasExtension(file_id, allowed)) {
        hide_busy();
        do_growl("This file type is not allowed.",'danger');
        return false;
    }
    
    subtitle =  $('#block-subtitle-'+block_id).val();
    file = file.files[0];
    data = new FormData();
    data.append('block', block_id);
    data.append("file", file);
    data.append("no_file", no_file);
    
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL+"/lessons/block/save_file",
        cache: false,
        contentType: false,
        processData: false,
                xhr: function()
          {
            $('#current_progress').remove();
            if(no_file==0){ 
                $('#block-'+block_id+' [type="file"]:visible').first().after( $('.progress').clone().attr('id','current_progress'));
            }
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
            if(response.indexOf('--error--')!=-1){
                response = response.replace('--error--','');
                do_growl(response, 'danger');    
                hide_busy();
                
                $('#current_progress .indicator').addClass('progress-bar-danger');
                return false;
            }
            else{
                $('#current_progress').remove();
                content = response;
                title =  $('#block-title-'+block_id).val();
                subtitle =  $('#block-subtitle-'+block_id).val();
                $('#block-title-span-'+block_id).html(title);
                var in_section = $('#in_section-'+block_id).val();
                category_id =  $('#category-'+block_id).val();
                if(no_file==0) data = {text:content, title:title, subtitle:subtitle, in_section:in_section, category_id:category_id}
                else data = {title:title, subtitle:subtitle, in_section:in_section, category_id:category_id}
                $.post(url,data, function(result){
                       hide_busy();
                       result = parse_json(result);
                       if(!result) return false;
                       if(!no_growl) do_growl(result.text, result.status); 
                       if(result.status=='success' ){
                           if(no_file==0) $('#block-'+block_id+' .file_link').html("Download: <a href='"+APP_URL+'/assets/downloads/'+content+"' target='_blank'>"+title+"</a>");
                           else $('#block-'+block_id+' .file_link a').html(title);
                           if(title=='') $('#block-'+block_id+' .file_link a').html('[YOUR FILE]');
                       }
                });
            }
            
        }
    });
}



function save_question_block(block_id, url){
    show_busy();
    title =  $('#block-title-'+block_id).val();
    subtitle =  $('#block-subtitle-'+block_id).val();
    $('#block-title-span-'+block_id).html(title);
    var answer_type = $('#block-answer-type-'+block_id).val();
    var minimum_choices = maximum_choices = 0;
    var choices = the_options = '';
    var scale_min = scale_max = 0;
    var scale_min_text = scale_max_text = '';
    var entries = '';
    var required = 0;
    var in_section = $('#in_section-'+block_id).val();
    category_id =  $('#category-'+block_id).val();
    
    if(answer_type=='Open Ended'){
        required = $('#block-answer-required-'+block_id+':checked').length;
        scale_min_text = $('.open-length-'+block_id+':checked').val();
    }
    else if(answer_type=='Skill Select'){
        var minimum_choices = $('#block-answer-min-skill-'+block_id).val();
        var maximum_choices = $('#block-answer-max-skill-'+block_id).val();
    }
    else if(answer_type=='Scale'){
        var scale_min = $('#block-answer-scale-min-'+block_id).val();
        var scale_max = $('#block-answer-scale-max-'+block_id).val();
        var minimum_choices = $('#block-answer-scale-per-tab-'+block_id).val();
        if(scale_max <= scale_min){
            hide_busy();
            do_growl("The scale max value needs to be greater than the min value",'danger');
            return false;
        }
        scale_min_text = $('#block-answer-scale-min-text'+block_id).val();
        scale_max_text = $('#block-answer-scale-max-text'+block_id).val();
        var choices = $('#scale-options-holder-'+block_id+' .option-input');
        if($(choices).length < 1){
            hide_busy();
            do_growl("At least one entry should be included",'danger');
            return false;
        }
        var choices_valid = true;
        entries = new Array();
        $(choices).each(function(){
            if( $.trim( $(this).val()) ==''){
                choices_valid = false;
                return false;
            }
            entries.push($(this).val());
        });
        if(choices_valid==false){
            do_growl("No blank entries are allowed",'danger');
            hide_busy();
            return false;
        }
        entries =  JSON.stringify(entries);
    }
    else{
        var maximum_choices = $('#block-answer-max-choice-'+block_id).val();
        var minimum_choices = $('#block-answer-min-choice-'+block_id).val();
        if(maximum_choices=='') maximum_choices = 99999;
        if( !isNaN(maximum_choices) && maximum_choices!='' && maximum_choices< minimum_choices){
            hide_busy();
            do_growl("The maximum choices value needs to be equal or greater than "+minimum_choices,'danger');
            return false;
        }
        var choices = $('#options-holder-'+block_id+' .option-input');
        if($(choices).length < 1){
            hide_busy();
            do_growl("At least one answer choice should be offered",'danger');
            $('#block-'+block_id).effect("highlight", {}, 2000);
            return false;
        }
        var the_options = new Array();
        var choices_valid = true;
        $(choices).each(function(){
            if( $.trim( $(this).val()) ==''){
                choices_valid = false;
                return false;
            }
            the_val = nl2br($(this).val());
            the_options.push(the_val);
        });
        if(choices_valid==false){
            do_growl("No blank options are allowed",'danger');
            hide_busy();
            return false;
        }
        the_options =  JSON.stringify(the_options);
    }
    console.log(scale_min + ' ' +scale_min_text + ' ' +scale_max + ' ' +scale_max_text + ' ' +entries );
    $.post(url,{title:title, 
                subtitle:subtitle, 
                answer_type:answer_type, 
                maximum_choices:maximum_choices, 
                minimum_choices:minimum_choices, 
                choices:the_options,
                scale_min:scale_min,
                scale_min_text:scale_min_text,
                scale_max:scale_max,
                scale_max_text:scale_max_text,
                scale_entries:entries,
                required:required,
                in_section:in_section,
                category_id:category_id
            }, function(result){
           hide_busy();
           result = parse_json(result);
            if(!result) return false;
            do_growl(result.text, result.status); 
    });
}

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function sendFile(file,editor,welEditable) {
    do_growl('Uploading Image - please wait...','info');
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL+"/lessons/block/saveimage",
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
                if(response=='error'){
                        do_growl('Error saving image', 'danger');    
                        return false;
                }
                do_growl('Upload successful','success');
                editor.insertImage(welEditable, response);
        }
    });
}

function question_options(id){
    var current_option = $('#block-answer-type-'+id).val();
    $('.question-type').hide();
    if(current_option=='Multiple Choice'){
        $('#question-options-'+id).show();
    }
    else if(current_option=='Scale'){
        $('#question-scale-'+id).show();
    }
    else if(current_option=='Open Ended'){
        $('#question-open-'+id).show();
    }
    else{
         $('#question-skill-'+id).show();
    }
}

function add_question_option(id){
    do{
        d = new Date().getTime();
        var new_id = 'option-'+ d;
    }
    while($('#'+new_id).length >0);
    $('#question-options-'+id+' .options-holder').append('<div class="option" id="'+new_id+'"><div class="option-number">0</div><div class="option-value"><input type="text" placeholder="Option" class="option-input" value="" /></div><div class="option-cancel"><span onclick="remove_option(\''+new_id+'\')"><i class="mainsprite sprite_close2"></i></span></div></div>');
    recalculate_option_labels(id);
}

function recalculate_option_labels(id){
    i = 1;
    if(isNaN(id)) id = '#'+id;
    else id = "#block-"+id;    
    
    $(id+" .option-number").each(function(){
        $(this).html(i);
        ++i;
    });
}


function add_scale_option(id){
    do{
        d = new Date().getTime();
        var new_id = 'scale-option-'+ d;
    }
    while($('#'+new_id).length >0);
    
    //$('#question-scale-'+id+' .scale-options-holder').append('<div class="option" id="'+new_id+'"><div class="option-number">0</div><div class="option-value"><input type="text" placeholder="Option" class="option-input" value="" /></div><div class="option-cancel"><span onclick="remove_option(\''+new_id+'\')"><i class="mainsprite sprite_close2"></i></span></div></div>');
    $('#scale-options-holder-'+id).append('<div class="option" id="'+new_id+'"><div class="option-number">0</div><div class="option-value"><input type="text" placeholder="Option" class="option-input" value="" /></div><div class="option-cancel"><span onclick="remove_option(\''+new_id+'\')"><i class="mainsprite sprite_close2"></i></span></div></div>');
    //$('#question-scale-'+id+' .scale-options-holder').append('<div id="'+new_id+'">    <div class="input-group">      <input type="text" placeholder="Entry" class="form-control option-input">      <span class="input-group-btn">        <button class="btn btn-danger" type="button" onclick="remove_option(\''+new_id+'\')"><i class="glyphicon glyphicon-minus"></i></button>      </span>    </div><!-- /input-group -->  <br /></div><!-- /.col-lg-6 -->');
    recalculate_option_labels(id);
}

function remove_option(id){
    parent = $('#'+id).parent().parent().parent().parent().attr('id');
    $('#'+id).remove();
    recalculate_option_labels(parent);
}

function add_new_chapter(id){
    bootbox.dialog({
      message: loader_gif,
      title: "Add New Chapter"
    });
     $('.bootbox-body').load(APP_URL + '/lessons/create_chapter/'+id, function(data) {
        $('#add_form').bootstrapValidator({
            submitHandler: function(validator, form, submitButton) {
                if($('#chapter_order').val()=='at_the_end'){
                    
                }
                else{
                    if($('#chapter_ref').val()==0){
                        do_growl('Please select the reference chapter','danger');
                        $('#chapter_ref').effect('shake');
                        $('#add_form').data('bootstrapValidator').resetForm();
                        return false;
                    }
                }
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
                        $('#add_form').data('bootstrapValidator').resetForm();
                        $('#add_form')[0].reset();
                        bootbox.hideAll();
                        $('#chapter_id').editable('setValue', result.id);
                        
                    }
                    else {
                        $('#add_form').data('bootstrapValidator').resetForm();
                    }
                    hide_busy();
                });
            }});
    });
}


function add_category(){
    bootbox.dialog({
      message: loader_gif,
      title: "Add New Category"
    });
     $('.bootbox-body').html("<input type='text' class='form-control' placeholder='Category name' id='category_name' /><button type='button' class='btn btn-primary' onclick='do_add_cat()'>Add</button>");       
}

function do_add_cat(){
    if($.trim($('#category_name').val())==''){
        do_growl('Please enter a category name','danger');
        return false;
    }
    show_busy();
    var label = $('#category_name').val();
    bootbox.hideAll();
    $.post(APP_URL + '/lessons/add_category',{cat:label},function(data){
        $('.category').append(
            $('<option></option>').val(data).html(label )
        );
        $('#category_name').val('');
        
        hide_busy();
        do_growl('Category added','success');
        return false;
    });
    return false;
}
var tst;
function add_tag(block){
    str = '';
    //"Select the answer you want to generate a tag for: <select id='tag_list'>"+str+"</select>",
    bootbox.dialog({
        message: 'Loading - Please wait...',
        title: "Select Answer",
        buttons: {
            add: {
                label: "Add Tag",
                className: "btn-primary",
                callback: function() {
                   do_add_tag(block);
                }
            }
        }
    });
    $.get(APP_URL+"/lessons/get_answers",function(data){
        $('.bootbox-body').html(data);
    });
    
}
var is_report = 0;
function add_report_tag(block){
    str = '';
    //"Select the answer you want to generate a tag for: <select id='tag_list'>"+str+"</select>",
    bootbox.dialog({
        message: 'Loading - Please wait...',
        title: "Select Answer",
    });
    $.get(APP_URL+"/lessons/get_answers/1",function(data){
        $('.bootbox-body').html(data);
    });
    
}

function show_tag_val(){
    id = $('#answers_tag').val();
    $('#tag_val').html("[var="+id+"]");// include_header=1
}

function do_add_tag(block){
    var cursorPos = window.selection.anchorOffset;
    var oldContent = window.selection.anchorNode.nodeValue;
    var toInsert = " [var="+$('#answers_tag').val()+"] ";
    if(is_report==1) toInsert = " [var="+$('#answers_tag').val()+" report=1] ";
    if(oldContent!=null){
        var newContent = oldContent.substring(0, cursorPos) + toInsert + oldContent.substring(cursorPos);
        window.selection.anchorNode.nodeValue = newContent;
    }
    else{
        html = $('#block-'+block+' .summernote_editor').code();
        html+= toInsert;
        $('#block-'+block+' .summernote_editor').code(html);
    }
    is_report = 0;
}

function tag_options() {
    str = "Example: [var=123 delim=- pos=3]<br /><br /><b>var</b> = The ID of the answer to reference<br /><b>delim</b> = In case the answer is a list, the specified separator will be used. Otherwise default to new line";
    str += "<br /><b>pos</b> = Show only the answer in the POS position in the list";
    str += "<br /><b>include_rating</b> = For scale answers - If 'yes', show the rating of the answer";
    str += "<br /><b>height</b> = Height in px or percentage for uploaded images (e.g. height=40 OR height=23%)";
    str += "<br /><b>width</b> = Width in px or percentage for uploaded images (e.g. width=40 OR width=23%)";
    str += "<br /><br /><b>[CLIENT_NAME]</b> displays the client name";
    bootbox.alert(str);
}

function toggle_publish(lesson, url){
    show_busy();
    if($('#publish_btn').html()=='Publish'){
        html = 'Unpublish';
        removeClass = 'btn-success';
        addClass = 'btn-danger';
        val = 1;
        msg = 'The lesson has been published';
        $('#public_view_link').show();
    }
    else{
        html = 'Publish';
        addClass = 'btn-success';
        removeClass = 'btn-danger';
        val = 0;
        msg = 'The lesson has been unpublished';
        $('#public_view_link').hide();
    }
    $.post(url,{pk:lesson, name:'published', value:val}, function(data){
        hide_busy();
        $('#publish_btn').removeClass(removeClass);
        $('#publish_btn').addClass(addClass);
        $('#publish_btn').html(html);
        do_growl(msg, 'success');
    });
}

function advanced_lesson_settings(){
    $(".advanced_lesson").toggle();
    if($('.advanced_lesson:visible').length==0){
        $('#advanced_settings_btn').html("Advanced Settings <i class='glyphicon glyphicon-chevron-down'></i>");
    }
    else{
        $('#advanced_settings_btn').html("Advanced Settings <i class='glyphicon glyphicon-chevron-up'></i>");
    }

}

add_element_url = '';
function add_page_element(url){
  add_element_url = url;
  bootbox.dialog({
      message: loader_gif,
      title: "Add Page Element"
    });   
  //$('.modal-dialog').css('width','900px');
  $('.bootbox-body').css('padding-bottom','50px');
  $('.bootbox-body').html('<h2>What do you want to add?</h2><div class="page_element" onclick="add_content()"><i class="glyphicon glyphicon-list-alt"></i><br />Content</div> <div class="page_element" onclick="add_question()"><i class="glyphicon glyphicon-question-sign"></i><br />Question</div>');
  $('.bootbox-body').addClass('text-center');
}

var element_position = 'x';
function end_new_page_element(){
    $('.adding').removeClass('pop-in');
    $('.adding').addClass('pop-out');
    setTimeout( function(){
        $('.adding').remove();
        $('.add_block_area').show();
    }, 200);
    return false;
}

function quick_end_new_page_element(){
    $('.adding').remove();
    $('.add_block_area').show();
}

add_element_id = 'z';
is_back_to_add = false;
function back_to_add(){
    is_back_to_add = true;
    add_new_page_element(add_element_url, add_element_id);
    is_back_to_add = false;
}
function add_new_page_element(url, id){
    add_element_id = id;
    $('.adding').remove();
    $('.add_block_area').show();
    pos = 1;
    $('.add-element-btn').each(function(){
        if($(this).hasClass('add-element-btn-'+id)){
            element_position = pos;// + 1;
        }
        ++pos;
    });
    if(id=='z') element_position = 'z';
   add_element_url = url;
   $('.add_block_area_'+id).hide();
   if(is_back_to_add) div = "<div class='adding'></div>";
   else div = "<div class='adding pop-in'></div>";
   if(id=='z'){
       $('.initial_area').after(div);
       $('.adding').css('margin-top','0px');
   }
   else $('#block-'+id).after(div);
    $('.adding').html( $('#adding-step-1').html() );
   console.log( $('.add-element-btn').length + 'vs '+element_position);
   if((element_position=='z' &&  $('.add-element-btn').length==1) || ( element_position == $('.add-element-btn').length)){
        setTimeout(function(){
             $('html, body').animate({
             scrollTop: $('.adding').offset().top - 10
         }, 100);
     }, 200);
   }
    
}

function add_question(){
     $('.adding').html( $('#adding-question').html() );
} 

function add_content(){
     $('.adding').html( $('#adding-content').html() );
     $('.do-tooltip').tooltip({container: 'body'});
}

function add_open_type(){
    $('.adding').html( $('#adding-open').html() );
}

function add_content_type(type){
     pos = (element_position==='x') ? $('#new_block_pos').val() : element_position ;
     console.log('position: '+pos);
     //$('.initial_area').hide();
     do_add_block(add_element_url, type, pos );
     
}
var program_name;
var lesson_name;

function create_program(step){
    if(step==1){
        $('.do-tooltip').tooltip('hide');
        fade_content('#create_program_div', $('#program').html() );
        setTimeout(function(){
            $('#program_name').focus(); 
        },310);
        
//        $('#create_program_div').fadeOut(300,function(){
//            $('#create_program_div').html( $('#program').html() );
//            $('#create_program_div').fadeIn(300);
//        });
    }
    else if(step==2){
        program_name = $('#program_name').val();
        if($.trim(program_name)==''){
            do_growl('Please enter a valid name', 'danger');
            return false;
        }
        //$('#create_program_div').html( $('#lesson').html() );
        fade_content('#create_program_div', $('#lesson').html() );
    }
    else{
//        lesson_name = $('#lesson_name').val();
//        if($.trim(lesson_name)==''){
//            do_growl('Please enter a valid name', 'danger');
//            return false;
//        }

        program_name = $('#program_name').val();
        if($.trim(program_name)==''){
            $('#program_name').focus(); 
            $('#program_name').css('border','2px solid #b53939'); 
            do_growl('Please enter a valid name', 'danger');
            return false;
        }
        $('.do-tooltip').tooltip('hide');
        fade_content('#create_program_div', 'Please wait...' );
        //show_busy();
        lesson_name = '';
        
        $.post(APP_URL+'/programs',{ 
            program:program_name, 
            lesson:lesson_name}, function(lesson){
            //window.location = APP_URL+'/lessons/'+lesson+'/editor';
            //window.location = APP_URL+'/modules';
            $('.tip').remove();
            fade_content('#create_program_div', lesson );
        });
    }
}

function toggle_chapter_ref(){
    if($('#chapter_order').val()!='at_the_end'){
        $('#chapter_ref').show();
    }
    else{
        $('#chapter_ref').hide();
    }
}
var no_growl = false;
function save_all(){
    no_growl = true;
    $(blocks_to_update).each(function(i,v){
        skip_timeout = true;
        $('#'+v+' .save-btn').first().click();
//        $('.save-btn').each(function(){
//            skip_timeout = true;
//            $(this).click();
//        });
    });
    blocks_string = (blocks_to_update.length==1) ? ' block' : ' blocks';
    msg_type = blocks_to_update.length==0 ? 'info' : 'success';
    do_growl(blocks_to_update.length + blocks_string + ' updated.', msg_type);
    blocks_to_update = new Array();
}

var blocks_to_update = new Array();
function blocks_changed(e){
    id = $(e.target).closest('.block').attr('id');
    if(blocks_to_update.indexOf(id)==-1) blocks_to_update.push(id);
    console.log(blocks_to_update);
}
