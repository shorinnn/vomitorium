$(function(){
    $('.table-modules').sortable({
          stop: function( event, ui ) {
              move = $(ui.item).attr('data-id');
              target = $(ui.item).prev('div').attr('data-id');
              console.log(move+ ' >> '+target);
              $( ".table-modules" ).sortable( "disable" );
               $.post( APP_URL+"/modules/move_chapter", {move:move, target:target}, function(result){
                   $( ".table-modules" ).sortable( "enable" );
               });
          }
        });
        
    $('.sortable-lessons').sortable({
          stop: function( event, ui ) {
              move = $(ui.item).attr('data-id');
              target = $(ui.item).prev('div').attr('data-id');
              console.log(move+ ' >> '+target);
              $( ".sortable-lessons" ).sortable( "disable" );
               $.post( APP_URL+"/modules/move_lesson", {move:move, target:target}, function(result){
                   $( ".sortable-lessons" ).sortable( "enable" );
               });
          }
        });
    $('body').on('click', '.add-lesson-btn', add_lesson);
    $('body').on('submit', '.add-module', add_module);
    $('body').on('click', '.new-module', show_add_module);
    $('button').removeAttr('disabled');
});

function show_add_module(){
     bootbox.dialog({
      message: loader_gif,
      title: "Add Module"
    });
    $('.bootbox-body').html( $('.new-module-form').html() );       
    options = "<option value='z'>AT THE BOTTOM</option><option value='y'>AT THE TOP</option>";
    $('.chapter a').each(function(){
        options += "<option value='"+$(this).attr('data-pk')+"'>AFTER  &laquo;"+$(this).text()+"&raquo;</option>";
    });
    $('.bootbox-body #position').html(options);
} 

function add_module(){
    name = $('.bootbox-body #module-name').val();
    lessons = $('.bootbox-body #lesson-count').val();
    if($.trim(name)==''){
        do_growl('Please specify the module name','danger');
        return false;
    }
    if(lessons < 1 || isNaN(lessons)){
        do_growl('You must create at least 1 lesson for this module','danger');
        return false;
    }
    
    var pos = $('.bootbox-body #position').val();
    
    data = $('.bootbox .add-module').serialize();
    $('.add-module input, .add-module button').attr('disabled', true);
    $('.add-module input, .add-module button').attr('readonly', true);
    $('.add-module input, .add-module button').addClass('disabled');
    $('.add-module button').html('<img src="' + APP_URL + '/assets/img/ajax-loader-transparent.gif" />');
    $.post($('.add-module').attr('action'), data, function(result){
        result = parse_json(result);
        if (!result) return false;
        if (result.status == 'success'){
            bootbox.hideAll();
            $('#module-name').val('');
            $('#lesson-count').val(1);
            if(pos=='z') $('.table-modules').append(result.html);
            else if(pos=='y') $('.table-modules').prepend(result.html);
            else $('#chapter-holder-'+pos).last().after(result.html);
            //$('.chapter-holder .chapter-'+pos).last().after(result.html);
            console.log(pos);
            
            $('.chapter-'+result.id).effect("highlight", {}, 3000);
        }
        else{}
        $('#module-name').focus();
        $('.add-module button').html('Add');
        $('.add-module input, .add-module button').removeAttr('disabled');
        $('.add-module input, .add-module button').removeAttr('readonly');
        $('.add-module input, .add-module button').removeClass('disabled');
        $('.editable').editable();
        do_growl(result.text, result.status);
    });
    
    return false;
}

function add_lesson(){
    id = $(this).attr('data-id');
    url = $(this).attr('data-url');
    $.post(url,{id:id}, function(result) {
        result = parse_json(result);
        // new animation code
        //$('.chapter-'+id).last().after(result.html);
        $('.chapter-'+id+' .sortable-lessons').last().append(result.html);
        $('.lesson-'+result.id).css('display','none');
        $('.lesson-'+result.id).slideToggle(400, function(){
            $('.lesson-'+result.id).effect("highlight", {}, 800);
        });
        $('.editable').editable();
        do_growl('Lesson Created', 'success');
        return false;
    });
}
