<?php

function editable_json($json, $value_key='name', $added_values=array()){
    $roles = array();
    if(count($added_values)>0){
        foreach($added_values as $key=>$val){
            $roles[$key] = $val;
        }
    }
    foreach($json as $j){
        $roles["_$j->id"] = $j->$value_key;
    }
    return json_encode($roles);
}

function admin(){
    if(Auth::guest() || !Auth::user()->hasRole('Admin')) return false;
    return true;
}

function extra_javascripts($meta){
    if(isset($meta['javascripts'])){
        $assets = '';
        foreach($meta['javascripts'] as $j)            $assets .= HTML::script($j);
        return $assets;
    }
}

function format_validation_errors($errors){
    return implode('<br />',$errors);
}

function move_up_class($ord){
    if ($ord <= 1) return ' disabled';
}

function move_down_class($ord, $max){
    if ($ord >= $max) return ' disabled';
}

function yes_no($int=0){
    return ($int==0) ? 'No' : 'Yes';
}

function bool_to_other($current, $v1, $v2){
    return ($current==0) ? $v1 : $v2;
}

function singplural($count, $word){
    if($count==1) return str_singular($word);
    else return str_plural ($word);
}

function disable_answered($answer){
    if($answer!==null) return 'disabled';
}

function format_date($date, $format = 'jS F Y H:i'){
    $date = strtotime($date);
    return date($format, $date);
}

function get_block_class($answer){
    if($answer!=null && $answer->attended==0 && admin()) return " unattended_block";
    return '';
}

function next_lesson($lesson){
    $url = '';
    // see if any other lessons in current chapter
    if(Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_id', $lesson->chapter_id)->where('ord','>',$lesson->ord)->count() > 0){
        $url = Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_id', $lesson->chapter_id)->where('ord','>',$lesson->ord)->orderBy('ord','ASC')->first();
    }
    else{
        // see if there are any lessons in the next chapter
        if(Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_id', '>', $lesson->chapter_id)->count() > 0){
            $url = Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_id', '>', $lesson->chapter_id)
                    ->orderBy('chapter_ord','ASC')->orderBy('ord','ASC')->first();
        }
    }
    if($url==null) return null;
    // see if lesson has been released
    if($url->release_type!='at_start' && !admin()){
        if($url->release_type=='on_date'){
            $date = strtotime($url->release_value);
            if(time() < $date) return null;
        }
        if($url->release_type=='after'){
            $start_date = DB::table('programs_users')->where('program_id', Session::get('program_id'))->where('user_id', Auth::user()->id)->first()->start_date;
            $release_date = strtotime("$start_date + $url->release_value day");
            $datetime1 = new DateTime( date("Y-m-d", $release_date) );
            $datetime2 = new DateTime();
            $interval = $datetime2->diff($datetime1)->format('%R%a');
            
            if((int) $interval > 0 || $interval ==='+0') return null;
        }
    }
    $url = $url->slug;
    if($url!=null && Session::has('user_id')) $url.= '/'.Session::get('user_id');
    return $url;
}

function previous_lesson($lesson){
    $url = '';
    // see if any other lessons in current chapter
    if(Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_id', $lesson->chapter_id)->where('ord','<',$lesson->ord)->count() > 0){
        $url = Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_id', $lesson->chapter_id)->where('ord','<',$lesson->ord)->orderBy('ord','DESC')->first()->slug;
    }
    else{
        // see if there are any lessons in the next chapter
        if(Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_ord', '<', $lesson->chapter_ord)->count() > 0){
            $url = Lesson::where('program_id',Session::get('program_id'))->where('published',1)->where('chapter_ord', '<', $lesson->chapter_ord)
                    ->orderBy('chapter_ord','DESC')->orderBy('ord','DESC')->first()->slug;
        }
    }
    if($url!=null && Session::has('user_id')) $url.= '/'.Session::get('user_id');
    return $url;
}

function parse_if_tags($text=''){
    
    if(Auth::guest()) $user_id = 0;
    else{
        $user_id = (admin() && Session::has('user_id')) ? Session::get('user_id') : Auth::user()->id;
    }
    
    preg_match_all("/\[if\](.*?)\[endif\]/", $text, $matches, PREG_PATTERN_ORDER);
    $matches = $matches[1];
    foreach($matches as $m){
        preg_match_all("/\[(.*?)\]/", $m, $conditions, PREG_PATTERN_ORDER);
        $conditions = $conditions[1];
        $replacement = $m;
        foreach($conditions as $c){
            $attr = explode(' ', $c);
            $id = 0;
            $is = '';
            foreach($attr as $a){
                if(substr($a, 0, 4) == 'var='){
                    $id = substr($a, 4);
                }
                if(substr($a, 0, 3) == 'is='){
                    $is = substr($a, 3);
                }
            }
            $replacement = str_replace("[$c]",'', $replacement);
        }
        $answer = Block_answer::where('user_id', $user_id)->where('block_id',$id)->first();
        if($is=='__empty'){
            if($answer==null || $answer->answer==''){
                $text = str_replace("[if]".$m."[endif]","$replacement", $text);
            }
            else{
                $text = str_replace("[if]".$m."[endif]","", $text);
            }
        }
        else{
            $a = (trim(strtolower($answer->answer)));
            $is = str_replace('-',' ', $is);
            $b = (trim(strtolower($is)));
            if($a==$b){
                $text = str_replace("[if]".$m."[endif]","$replacement", $text);
            }
            else{
                $text = str_replace("[if]".$m."[endif]","", $text);
            }
        }
        
    }
    return $text;
}

function parse_tags($text, $logo='', $report_title=''){

    $text = parse_if_tags($text);
    if(Auth::guest()) $user_id = 0;
    else{
        $user_id = (admin() && Session::has('user_id')) ? Session::get('user_id') : Auth::user()->id;
        $user_id = Auth::user()->id;
    }
    
    preg_match_all("/\[(.*?)\]/", $text, $matches, PREG_PATTERN_ORDER);
    $matches = $matches[1];
    foreach($matches as $m){
        if($m=='REPORT_LOGO'){
            $text = str_replace("[$m]",$logo, $text);
            continue;
        }
        if($m=='REPORT_TITLE'){
            $text = str_replace("[$m]",$report_title, $text);
            continue;
        }
        if($m=='CLIENT_NAME'){
            $name = User::find($user_id)->username;
            $text = str_replace("[$m]",$name, $text);
            continue;
        }
        if($m=='CLIENT_FIRSTNAME'){
            $name = User::find($user_id)->first_name;
            $text = str_replace("[$m]",$name, $text);
            continue;
        }
        if($m=='CLIENT_LASTNAME'){
            $name = User::find($user_id)->last_name;
            $text = str_replace("[$m]",$name, $text);
            continue;
        }
        $attr = explode(' ', $m);
        $id = 0;
        $delim = '<br />';
        $skill_type = '';
        $include_rating = 'no';
        $pos = false;
        $include_header = false;
        $height = $width = 0;
        foreach($attr as $a){
            $a = str_replace('&nbsp;', '', $a);
            $a = strip_tags($a);
            if(substr($a, 0,15 ) == 'include_header='){
                $include_header = substr($a, 15);
            }
            if(substr($a, 0, 4) == 'var='){
                $id = substr($a, 4);
            }
            if(substr($a, 0,6 ) == 'delim='){
                $delim = substr($a, 6);
            }
            if(substr($a, 0,6 ) == 'skill='){
                $skill_type = substr($a, 6);
            }
           
            if(substr($a, 0,4 ) == 'pos='){
                $pos = substr($a, 4);
                $pos = (int)$pos;
                $pos--;
                if($pos<0) $pos = 0;
            }
            if(substr($a, 0,15 ) == 'include_rating='){
                $include_rating = substr($a, 15);
            }
            if(substr($a, 0,6 ) == 'width='){
                $width = substr($a, 6);
            }
            if(substr($a, 0,7 ) == 'height='){
                $height = substr($a, 7);
            }
        }
        
        $str = '';
        $answer = Block_answer::where('user_id', $user_id)->where('block_id',$id)->first();
        $header = '';
        if(Block::find($id)!=null) $header = Block::find($id)->title;
        
        if($answer!=null){
            if(Block::find($id)->type=='question'){
                switch(Block::find($id)->answer_type){
                    case 'Open Ended': 
                        $str = $answer->answer;
                        break;
                    
                    case 'Skill Select': 
                        $answer = json_decode($answer->answer, true);
                        $str = $answer[$skill_type];
                        if($pos===false) $str = implode("$delim ", $str);
                        else{ 
                            if($pos > count($str)-1) $pos = count($str)-1;
                            $str = $str[$pos];
                        }
                        break;
                    case 'Multiple Choice':
                        $str = json_decode($answer->answer, true);
                        $str = array_values($str);
                        if($pos===false) $str = implode("$delim ", $str);
                        else{ 
                            if($pos > count($str)-1) $pos = count($str)-1;
                            $str = $str[$pos];
                        }
                        break;
                    case 'Scale':
                        $answer = json_decode($answer->answer);
                        $arr = array();
                        foreach($answer as $a){
                            if($include_rating=='yes') $arr[] = $a->option.' (rated '.$a->rated.')';
                            else    $arr[] = $a->option;
                        }
                        if($pos===false) $str = implode("$delim ", $arr);
                        else {
                            if($pos > count($arr)-1) $pos = count($arr)-1;
                            $str = $arr[$pos];
                        }
                        break;
                    default: $str = $answer->answer;
                }
            }
            elseif(Block::find($id)->type=='image_upload'){
                $height = $height==0 ? '' : "height='$height'";
                $width = $width==0 ? '' : "width='$width'";
                $str = "<img $height $width class='uploaded_image' src='".url('assets/uploads/'.$answer->answer)."' />";
            }
            else{
                $answer = json_decode($answer->answer);
                $arr = array();
                foreach($answer as $a){
                    $arr[] = $a->option;
                }
                if($pos===false) $str = implode("$delim ", $arr);
                else {
                    if($pos > count($arr)-1) $pos = count($arr)-1;
                    $str = $arr[$pos];
                }
            }
        }
        if($include_header) $str = "<p class='report-header'>$header</p>".$str;
        $text = str_replace("[$m]",$str, $text);
    }
    return $text;
}

function sys_settings($setting='installation', $default=''){
    $setting = DB::table('settings')->first()->$setting;
    if($setting=='' && $default!='') $setting = $default;
    return $setting;
}

function current_controller()
    {
        $routeArray = Str::parseCallback(Route::currentRouteAction(), null);

        if (last($routeArray) != null) {
            // Remove 'controller' from the controller name.
            $controller = str_replace('Controller', '', class_basename(head($routeArray)));
            return $controller;

            // Take out the method from the action.
            $action = str_replace(array('get', 'post', 'patch', 'put', 'delete'), '', last($routeArray));

            return Str::slug($controller . '-' . $action);
        }

        return 'closure';
    }
    
function br2nl($str) {
    $str = preg_replace("/(\r\n|\n|\r)/", "", $str);
    return preg_replace("=<br */?>=i", "\n", $str);
}


function get_programs(){
    return Program::all();
}

function format_icon($filename){
    $file = app_path('assets/downloads/'.$filename);
    $filesystem = new Illuminate\Filesystem\Filesystem();
    $extension = $filesystem->extension($file);
    if($extension == 'doc'){
        return url('assets/img/download-doc.png');
    }
    else if($extension == 'zip'){
        return url('assets/img/download-zip.png');
    }
    else if($extension == 'pdf'){
        return url('assets/img/download-pdf.png');
    }
    else{
        return url('assets/img/download-file.png');
    }
    
}

function toByteSize($p_sFormatted) {
    $aUnits = array('B'=>0, 'KB'=>1, 'MB'=>2, 'GB'=>3, 'TB'=>4, 'PB'=>5, 'EB'=>6, 'ZB'=>7, 'YB'=>8);
    $sUnit = strtoupper(trim(substr($p_sFormatted, -2)));
    if (intval($sUnit) !== 0) {
        $sUnit = 'B';
    }
    if (!in_array($sUnit, array_keys($aUnits))) {
        return false;
    }
    $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
    if (!intval($iUnits) == $iUnits) {
        return false;
    }
    return $iUnits * pow(1024, $aUnits[$sUnit]);
}

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function client_term(){
     if(sys_settings('client_term')=='') return 'Clients';
     return sys_settings('client_term');
}

function human_dash(){
    switch(sys_settings('dash_layout')){
        case 'template_1': return 'Full'; break;
        case 'template_2': return 'Unattended & Messages'; break;
        case 'template_3': return 'Except Messages'; break;
        default: return 'Full'; break;
    }
}