function create_report(){
    html = "Please set the report details:<br />";
    html += "Title <input type='text' id='report_title' class='form-control' placeholder='Title' /><br />";
   //html += "Logo <input type='file' id='logo' class='form-control'  /><br />";
    html += "Template <input type='file' id='template' class='form-control'  /><br />";
    html += "<button id='submit_btn' class='btn btn-primary' onclick='do_create_report()'>Create</button>";
     bootbox.dialog({
        message: html,
        title: "Report Properties"
    });
}

function do_create_report(){
    title =  document.getElementById('report_title');
    if(title.value==''){
        do_growl('Cannot create a report with a blank title','danger');
        $('#report_title').effect('shake');
         return false;
    }
    template = document.getElementById('template');
    if(template.value==''){
        do_growl('Cannot create report. Please upload a template file','danger');
        $(template).effect('shake');
        return false;
    }
     if( !hasExtension('template', ['.html','.txt']) ){
        do_growl('The template supplied is not a supported format (.html or .txt)','danger');
        $(template).effect('shake');
        return false;
     }
    logo = document.getElementById('logo');
//    var no_logo = false;
//    if(logo.value==''){
//        conf = confirm("You have not selected a logo image. Create report with no logo?");
//        if(conf==false) return false;
//        no_logo = true;
//    }
//    else{
//         if( !hasExtension('logo', ['.png','.jpg','.jpeg','.gif','.bmp']) ){
//            do_growl('The logo supplied is not a supported format (.jpg, .gif, .png or .bmp)','danger');
//            $(logo).effect('shake');
//            return false;
//         }
//    }
    no_logo = true;
    $('input').attr('disabled','disabled');
    $('#submit_btn').attr('disabled','disabled');
    $('#submit_btn').html('Creating report. Please wait...');
    do_growl('Please wait...','info');
    
    template = template.files[0];
    data = new FormData();
    data.append('title', title.value);
    data.append("template", template);
    if(no_logo==false) {
        logo = logo.files[0];
        data.append('logo', logo);
    }
    $.ajax({
        data: data,
        type: "POST",
        url: APP_URL+"/reports/create",
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#submit_btn').removeAttr('disabled');
            $('#submit_btn').html('Create');
            console.log(response);
            response = parse_json(response);
            if(!response){
                $('input').removeAttr('disabled');
                $('#submit_btn').removeAttr('disabled');
                return false;
            }
            if(response.success==1){
                 do_growl('Report created. <a href="'+response.url+'" target="_blank">View Report</a>','success');
                 $('.table').append(response.html);
                 bootbox.hideAll();
            }
            else{
                $('input').removeAttr('disabled');
                $('#submit_btn').removeAttr('disabled');
                do_growl(response.error,'danger');
                
            }
        }
    });
     
}
is_lesson_editor = true;
