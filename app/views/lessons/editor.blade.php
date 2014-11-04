@extends($layout)

@section('content')

        <div class="container">
            <h3 style="margin-bottom: 10px;">
                <!--<a href='{{url('lessons')}}'><i class='glyphicon glyphicon-chevron-left'></i> Back to Lesson Manager</a>-->
                <a href='{{url('modules')}}'><i class='glyphicon glyphicon-chevron-left'></i> Back to Modules</a>
                <a id='public_view_link' href="{{url("lessons/view_lesson/".$data['lesson']->id)}}"  target="_blank" class='btn btn-primary'><i class="glyphicon glyphicon-eye-open"></i> Public view</a>
            </h3>
            <table class="table">
                <tr><td style='width:30%'>Title</td>
                    <td><a class="editable" href="#" id="title" data-type="text" data-pk="{{$data['lesson']->id}}" 
                       data-name="title" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter Title" data-mode='inline'>{{$data['lesson']->title}}
                      </a>
                    </td>
                </tr>
                <tr><td>Deadline</td>
                    <td>
                        <button class="btn btn-primary deadline-btn" onclick="slideToggle('.deadline')">Set Deadline</button>
                        <div class="deadline nodisplay">
                            <div class='padded-top'>
                                {{Form::select('deadline-trigger', array('after-enrollment' => 'After Enrollment', 
                                                                            'after-completion' => 'After Completion', 
                                                                            'after-release' => 'After Release',
                                                                            'on-date' => 'On specific date'), 
                                        $data['lesson']->deadline_type, 
                                        array(
                                            'id'=>'deadline-trigger', 'onchange'=> "change_deadline_trigger(this)", 'class'=>'form-control',
                                            'data-url'=>action("LessonsController@update",array(1)),'data-pk'=>$data['lesson']->id
                                            )
                                        ) }}
                            </div>
                            <?php
                            $interval_count = $interval_type = $interval_lesson = $on_date = '';
                            if($data['lesson']->deadline_type=='after-enrollment'){
                                $json = json_decode($data['lesson']->deadline_value);
                                $interval_count = $json->count;
                                $interval_type = $json->unit;
                            }
                            if($data['lesson']->deadline_type=='after-completion' || $data['lesson']->deadline_type=='after-release' ){
                                $interval_lesson = $data['lesson']->deadline_value;
                            }
                            if($data['lesson']->deadline_type=='on-date'){
                                 $on_date = $data['lesson']->deadline_value;
                            }
                            ?>
                            <div class="after-enrollment text-left padded-top deadline-div">
                                <div class="col-lg-2 text-left nopadd">
                                    <input type='text' class='form-control' id="interval_count" placeholder="7"
                                   onkeyup='update_deadline_data(this)'  value="{{$interval_count}}"
                                   onchange='update_deadline_data(this)' data-pk = '{{$data['lesson']->id}}' data-url="{{action("LessonsController@update",array(1))}}" />
                                </div>
                                <div class="col-lg-4">
                                    {{Form::select('interval-type', array('day' => 'day(s)', 'week' => 'week(s)', 'month' => 'month(s)'), 
                                        $interval_type, 
                                        array(
                                            'id'=>'interval-type', 'onchange'=>'update_deadline_data(this)', 'class'=>'form-control',
                                            'data-url'=>action("LessonsController@update",array(1)),'data-pk'=>$data['lesson']->id
                                            )
                                        ) }}
                                </div>
                                <div class="col-lg-6 text-left"><p class='padded-top'>After enrollment</p></div>
                                <div class="clearfix"></div>
                            </div>
                            
                            <div class="after-completion after-release text-left padded-top nodisplay deadline-div">
                                <select class='form-control' id="interval-lesson"
                                        onchange='update_deadline_data(this)' data-pk = '{{$data['lesson']->id}}' data-url="{{action("LessonsController@update",array(1))}}">
                                    <?php
                                        $chapter = '';
                                        foreach($data['lessons'] as $l){ 
                                            if($l->chapter_id>0 && $chapter!=$l->chapter->title){
                                                $chapter = $l->chapter->title;
                                                echo "<option disabled>".strtoupper($chapter)."</option>";
                                            }
                                            $checked = '';
                                            if($l->id==$interval_lesson) $checked=' selected';
                                            echo "<option value='$l->id' $checked>&nbsp;&nbsp;&nbsp;$l->title</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            
                             <div class="on-date text-left padded-top nodisplay deadline-div ">
                                 <input type="text" class="form-control form-inline date-field" id="deadline_date" name="deadline_date" 
                                        value="{{$on_date}}"
                                        onchange='update_deadline_data(this)' data-pk = '{{$data['lesson']->id}}' 
                                        data-url="{{action("LessonsController@update",array(1))}}" style='width:30%; float:left' />
                             </div>
                            <br />
                            <button class="btn btn-primary" onclick="deadline_notifications({{$data['lesson']->id}})">Deadline Notifications</button>
                        </div>
                    </td>
                </tr>
                <tr class='advanced_lesson'>
                    <td>Chapter</td>
                    <td>
                        <a class="editable" href="#" id="chapter_id" data-type="select" data-pk="{{$data['lesson']->id}}" data-source='{{url('lessons/all_chapters')}}' 
                           data-value="_{{$data['lesson']->chapter_id}}" data-name="chapter_id" data-url="{{action("LessonsController@update", array($data['lesson']->id))}}"
                           data-original-title="Enter Chapter" data-mode='inline'>{{$data['lesson']->chapter->title or 'None'}}
                        </a>
                        <button type="button" class="btn btn-primary pull-right" onclick="add_new_chapter({{$data['lesson']->id}})">Add New Chapter</button>
                        
                    </td>
                </tr>

                <tr class='advanced_lesson'>
                    <td>Permalink 
                        <span id='permalink-tooltip' class='do-tooltip' title='Lesson URL will be {{url('lesson/'.$data['lesson']->slug)}}'>[?]</span></td>
                    <td><a class="editable" href="#" id="slug" data-type="text" data-pk="{{$data['lesson']->id}}" 
                       data-name="slug" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter URL" data-mode='inline'>{{$data['lesson']->slug}}
                      </a>
                    </td>
                </tr>
<!--                <tr class='advanced_lesson'>
                    <td>Keywords</td>
                    <td><a class="editable" href="#" id="meta_keywords" data-type="text" data-pk="{{$data['lesson']->id}}" 
                       data-name="meta_keywords" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter Keywords" data-mode='inline'>{{$data['lesson']->meta_keywords}}
                      </a>
                    </td>
                </tr>
                <tr class='advanced_lesson'>
                    <td>Description</td>
                    <td><a class="editable" href="#" id="meta_description" data-type="textarea" data-pk="{{$data['lesson']->id}}" 
                       data-name="meta_description" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter Description" data-mode='inline'>{{$data['lesson']->meta_description}}
                      </a>
                    </td>
                </tr>-->
                <tr class='advanced_lesson'>
                    <td>Blocks Per Section (If sections are used)</td>
                    <td><a class="editable" href="#" id="section_capacity" data-type="text" data-pk="{{$data['lesson']->id}}" 
                       data-name="section_capacity" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter Blocks Per Section" data-mode='inline'>{{$data['lesson']->section_capacity}}
                      </a>
                    </td>
                </tr>
                <tr class='advanced_lesson'>
                    <td>Release Lesson</td>
                    <td>
<!--                        <select class="form-control form-inline" id="release" name="release" 
                            onchange="release_options()">
                            <option value="at_start">at start</option>
                            <option value="on_date">on date</option>
                            <option value="after">after</option>
                        </select>-->
                        
                        {{Form::select('release', array('at_start' => 'at start', 'on_date' => 'on date', 'after' => 'after'), 
                                $data['lesson']->release_type, 
                                array(
                                    'id'=>'release', 'onchange'=>'release_options()', 'class'=>'form-control', 'data-field' => 'release_type',
                                    'data-url'=>action("LessonsController@update",array(1)),'data-pk'=>$data['lesson']->id
                                    )
                                ) }}
                        
                        <div id="release_date"  data-isdiv='1'>
                            <input type="text" class="form-control form-inline" id="date" name="date" style="width:100px;  float:left; margin-right:5px"
                                   @if($data['lesson']->release_type=='on_date' && DateTime::createFromFormat('m/d/Y', $data['lesson']->release_value))
                                   value="{{$data['lesson']->release_value}}"
                                   @endif
                                   onchange='update_release_data(this)' data-field = 'release_value' data-pk = '{{$data['lesson']->id}}'
                                   data-url="{{action("LessonsController@update",array(1))}}" />
                        </div>
                        <div id="release_days" data-isdiv='1'>
                            <input type="text" class="form-control form-inline" id="days" name="days" style="width:80px;  float:left; margin-right:5px"
                                   @if($data['lesson']->release_type=='after' && is_int($data['lesson']->release_value*1))
                                       value="{{$data['lesson']->release_value}}"
                                   @endif
                                   onchange='update_release_data(this)' data-field = 'release_value' data-pk = '{{$data['lesson']->id}}'
                                   data-url="{{action("LessonsController@update",array(1))}}"
                                   /> days
                        </div>
                    </td>
                </tr>
                <tr class='advanced_lesson'>
                    <td>Email clients on release</td>
                    <td>
                        <input type="checkbox" name="email_release" id="email_release"
                               @if($data['lesson']->release_email==1)
                               checked="checked"
                               @endif
                               onchange='update_release_data(this)' data-field = 'release_email' data-pk = '{{$data['lesson']->id}}' value='1'
                                   data-url="{{action("LessonsController@update",array(1))}}"
                               /><label for="email_release">Yes</label>
                        <button class="btn btn-primary pull-right" onclick='toggle_edit_email();'>Edit Email</button>
                    </td>
                </tr>
                <tr id='edit_email_rte'>
                    <td colspan='2' data-isrte="1">
                        <textarea id='edit_email_rte-content' class='summernote_editor'>{{$data['lesson']->release_email_content}}</textarea>
                        <br />
                        <button class='btn btn-default btn-sm' id='save_email'
                        onclick='update_release_data(this)' data-field = 'release_email_content' data-pk = '{{$data['lesson']->id}}'
                        data-url="{{action("LessonsController@update",array(1))}}">Save</button>
                        <p class="text-center"></p>
                    </td>
                </tr>
               <!-- <tr>
                    <td>Published</td>
                    <td>
                         <a class="editable" href="#" id="published" data-type="select" data-pk="{{$data['lesson']->id}}" data-source='{"0":"No", "1":"Yes"}' 
                           data-value="{{$data['lesson']->published}}" data-name="published" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}"
                           data-original-title="Enter Published" data-mode='inline'>{{yes_no($data['lesson']->published)}}
                        </a>
                    </td>
                </tr>-->
<tr><td colspan='2'><button id='advanced_settings_btn' class='btn btn-xs btn-primary' onclick='advanced_lesson_settings();'>
            Advanced Settings <i class='glyphicon glyphicon-chevron-down'></i></button></td></tr>
            </table>
            <!--onclick='add_block("{{action("LessonsController@add_block",array($data['lesson']->id))}}")'-->
            <!--<button class="btn btn-primary" onclick='add_page_element("{{action("LessonsController@add_block",array($data['lesson']->id))}}")'>Add Lesson Component</button>-->
            <!--<button class="btn btn-primary" onclick='add_page_element("{{action("LessonsController@add_block",array($data['lesson']->id))}}")'>Click Here Jerome(V2)</button>-->
            <div id="blocks_list">
                 <div class='add_block_area add_block_area_z initial_area'>
             <button class="btn btn-danger add-element-btn add-element-btn-z" onclick='add_new_page_element("{{action("LessonsController@add_block",array($data['lesson']->id))}}","z")'><i class='glyphicon glyphicon-plus'></i></button>
            </div>
                
                @foreach($data['blocks'] as $block)
                    @if($block->type=='text')
                        {{ View::make('lessons.text_block')->withBlock($block) }}
                    @elseif($block->type=='report')
                        {{ View::make('lessons.report_block')->withBlock($block)->withLessons($data['lessons']) }}
                    @elseif($block->type=='video')
                        {{ View::make('lessons.video_block')->withBlock($block)->withLessons($data['lessons']) }}
                    @elseif($block->type=='file')
                        {{ View::make('lessons.file_block')->withBlock($block)->withLessons($data['lessons']) }}
                    @elseif($block->type=='answer')
                        {{ View::make('lessons.answer_block')->withBlock($block)->withLessons($data['lessons']) }}
                    @elseif($block->type=='top_skills') 
                        {{View::make('lessons.top_skills')->withBlock($block)}}
                    @elseif($block->type=='dynamic') 
                        {{View::make('lessons.dynamic')->withBlock($block)}}
                    @elseif($block->type=='sortable') 
                        {{View::make('lessons.sortable')->withBlock($block)}}
                    @elseif($block->type=='category') 
                        {{View::make('lessons.category')->withBlock($block)}}
                    @elseif($block->type=='image_upload') 
                       {{View::make('lessons.image_upload_block')->withBlock($block)}}
                   @elseif($block->type=='file_upload') 
                       {{View::make('lessons.file_upload_block')->withBlock($block)}}
                    @else
                        @if($block->answer_type=='Score')
                            {{ View::make('lessons.score')->withBlock($block) }}
                        @else
                            {{ View::make('lessons.question_block')->withBlock($block) }}
                        @endif
                    @endif
                   
                @endforeach
           
            </div>
            @if($data['blocks']->count()==0)
<!--            <div class='add_block_area initial_area'>
             <button class="btn btn-success add-element-btn add-element-btn-z" onclick='add_new_page_element("{{action("LessonsController@add_block",array($data['lesson']->id))}}","z")'><i class='glyphicon glyphicon-plus'></i></button>
            </div>-->
            
             <!--<button style='display: none' class="btn btn-primary add-block-btn" onclick='add_block("{{action("LessonsController@add_block",array($data['lesson']->id))}}")'>Add Lesson Component</button>-->
             @else
<!--             <div class='add_block_area initial_area' style='display:none'>
             <button class="btn btn-success add-element-btn add-element-btn-z" onclick='add_new_page_element("{{action("LessonsController@add_block",array($data['lesson']->id))}}","z")'><i class='glyphicon glyphicon-plus'></i></button>
            </div>-->
             <!--<button class="btn btn-primary add-block-btn" onclick='add_page_element("{{action("LessonsController@add_block",array($data['lesson']->id))}}")'>Add Lesson Component</button>-->
             @endif
            
             @if($data['lesson']->published==1)
                 <button class='btn btn-danger' type='button' id='publish_btn' onclick='toggle_publish({{$data['lesson']->id}},"{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}")'>Unpublish</button>
             @else
                 <button class='btn btn-success' type='button' id='publish_btn' onclick='toggle_publish({{$data['lesson']->id}},"{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}")'>Publish</button>
             @endif
             <button class='btn btn-default inline' style='font-family: OSans'onclick='save_all();'>Save All</button>
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    <script>
        var is_lesson_editor = true;
    </script>
    <div style="display:none">
            <div id="adding-step-1">
                            <div class="row">
                                <div class="col-md-12">
                                  <div class="box">
                                    <div class="heading">
                                        <div class="closable mainsprite sprite_close"><span onclick="end_new_page_element()"></span></div>
                                        <h2>What do you want to add?</h2>
                                        <h3>Select an option.</h3>
                                    </div>
                                    <div class="box-content one-row two-column">
                                        <div class="row">
                                          <div class="col-xs-6 button_content">
                                            <button class="push-action"  onclick="add_content()">
                                                <i class="mainsprite sprite_content"></i>
                                                <span>Content</span>
                                            </button>       
                                          </div>
                                          <div class="col-xs-6 button_question">         
                                             <button class="push-action" onclick="add_question()">
                                                <i class="mainsprite sprite_question"></i>
                                                <span>Question</span>
                                            </button>       
                                          </div>
                                        </div>
                                        <div class="clearfix"></div>         
                                    </div><!-- end box content -->
                                  </div><!-- end box -->
                                </div><!-- end col-md-12-->
                              </div>
            </div>
       
        
        <div id="adding-questionz">

                <div class="row">
                        <div class="col-md-12">
                          <div class="box">
                            <div class="heading">
                                <div class="closable mainsprite sprite_close"><span onclick="end_new_page_element()"></span></div>
                                <h2>Select question type</h2>
                                <h3>Choose one.</h3>
                            </div>
                            <div class="box-content two-row two-column">
                                <div class="row">
                                  <div class="col-xs-6 button_open_ended">         
                                      <button class="push-action" onclick="add_open_type()">
                                          <i class="mainsprite sprite_open_ended"></i>
                                          <span>Open Ended</span>
                                      </button>              
                                  </div>  
                                  <div class="col-xs-6 button_1scale"> 
                                     <button class="push-action" onclick="add_content_type('scale')">
                                        <i class="mainsprite sprite_1scale"></i>
                                        <span>1-10 Scale</span>
                                    </button> 
                                  </div>
                                </div>            
                                <div class="second-row row">
                                   <div class="col-xs-6 button_multiple">  
                                      <button class="push-action"  onclick="add_content_type('mc')">
                                          <i class="mainsprite sprite_multiple"></i>
                                          <span>Multiple Choice</span>
                                      </button>  
                                    </div>  
                                    <div class="col-xs-6 button_sortable">             
                                     <button class="push-action" onclick="add_content_type('sortable')">
                                        <i class="mainsprite sprite_sortable"></i>
                                        <span>Sortable List</span>
                                    </button>             
                                  </div>
                                </div>

                                <div class="back-button"><span onclick="back_to_add()"><i class="sprite_l_arrow mainsprite"></i> Back</span></div>
                                <div class="clearfix"></div>
                            </div><!-- end box content -->
                          </div><!-- end box -->
                        </div><!-- end col-md-12-->
                      </div>
        </div>
        
        
        <div id="adding-question">

                <div class="row">
                    <div class="col-md-12">
                      <div class="box">
                        <div class="heading">
                            <div class="closable mainsprite sprite_close"><span onclick='end_new_page_element()'></span></div>
                            <h2>Add content</h2>
                            <h3>Choose the type of content.</h3>
                        </div>
                        <div class="box-content two-row three-column notpadd">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="center"> </div>
                              </div>                  
                            </div>
                            <div class="row slide-left-fade-in">
                              <div class="col-xs-4 button_texthtml">               
                                <button class="push-action" onclick="add_open_type()">
                                      <i class="mainsprite sprite_open_ended"></i>
                                      <span>Open Ended</span>
                                  </button>  
                              </div>
                              <div class="col-xs-4 button_video">               
                                 <button class="push-action" onclick="add_content_type('scale')">
                                        <i class="mainsprite sprite_1scale"></i>
                                        <span>1-10 Scale</span>
                                    </button>   
                             </div>
                              <div class="col-xs-4 button_filed">                           
                                <button class="push-action"  onclick="add_content_type('score')">    
                                    <i class="glyphicon glyphicon-plus" style="font-size:40px"></i>
                                    <span>Dynamic Score</span>
                                </button>             
                             </div>
                            </div> 
                            <div class="row slide-down-fade-in">
                              <div class="col-md-12">
                                <div class="center"> </div>
                              </div>                  
                            </div> 
                            <div class="row slide-left-fade-in slide-left-fade-in-2">
                              <div class="col-xs-4 button_sans">    
                                <button class="push-action"  onclick="add_content_type('mc')">
                                          <i class="mainsprite sprite_multiple"></i>
                                          <span>Multiple Choice</span>
                                      </button>  
                              </div>
                              <div class="col-xs-4 button_cond">                            
                                 <button class="push-action" onclick="add_content_type('sortable')">
                                        <i class="mainsprite sprite_sortable"></i>
                                        <span>Sortable List</span>
                                    </button>                         
                              </div>
                              <div class="col-xs-4">
                                   
                              </div>
                            </div>
                            
                            <div class="back-button"><span onclick="back_to_add()"><i class="sprite_l_arrow mainsprite"></i> Back</span></div>
                            <div class="clearfix"></div>
                        </div><!-- end box content -->
                      </div><!-- end box -->
                    </div><!-- end col-md-12-->
                  </div>
        </div>
        
        <div id="adding-content">

                <div class="row">
                    <div class="col-md-12">
                      <div class="box">
                        <div class="heading">
                            <div class="closable mainsprite sprite_close"><span onclick='end_new_page_element()'></span></div>
                            <h2>Add content</h2>
                            <h3>Choose the type of content.</h3>
                        </div>
                        <div class="box-content two-row three-column notpadd">
                            <div class="row slide-down-fade-in">
                              <div class="col-md-12">
                                <div class="center"> </div>
                              </div>                  
                            </div>
                            <div class="row slide-left-fade-in">
                              <div class="col-xs-4 button_texthtml">               
                                <button class="push-action" onclick="add_content_type('text')">
                                    <i class="mainsprite sprite_texthtml"></i>
                                    <span>Text/HTML</span>
                                </button> 
                              </div>
                              <div class="col-xs-4 button_video">               
                                 <button class="push-action"  onclick="add_content_type('video')">
                                    <i class="mainsprite sprite_video"></i>
                                    <span>Video</span>
                                </button>   
                             </div>
                              <div class="col-xs-4 button_filed">                           
                                <button class="push-action"  onclick="add_content_type('file')">    
                                    <i class="mainsprite sprite_filed"></i>
                                    <span>File Download</span>
                                </button>             
                             </div>
                            </div> 
                            <div class="row slide-down-fade-in">
                              <div class="col-md-12">
                                <div class="center"> </div>
                              </div>                  
                            </div> 
                            <div class="row slide-left-fade-in slide-left-fade-in-2">
                              <div class="col-xs-4 button_sans">    
                                <button class="push-action do-tooltip"  
                                        data-placement="top" 
                                        onclick="add_content_type('sortable_skills')"
                                        title="Choose a previous question and sort and display the answers">
                                    <i class="mainsprite sprite_sans"></i>
                                    <span>Sorted Answers</span>
                                </button>  
                              </div>
                              <div class="col-xs-4 button_cond">                            
                                 <button class="push-action"  onclick="add_content_type('dynamic')">
                                    <i class="mainsprite sprite_cond"></i>
                                    <span>Conditional</span>
                                </button>                          
                              </div>
                              <div class="col-xs-4">
                                  <button class="push-action"  onclick="add_content_type('image_upload')">
                                    <span>Image Upload</span>
                                </button>    
                              </div>
                            </div>
                            <div class="row ">
                              <div class="col-md-12">
                                <div class="center"></div>
                              </div>                  
                            </div> 
                            <div class="row slide-left-fade-in slide-left-fade-in-3">
                              <div class="col-xs-4 button_sans">    
                                <button class="push-action"  onclick="add_content_type('top_skills')">
                                    <span>Top Skills</span>
                                </button>    
                              </div>
                              <div class="col-xs-4 button_cond">                            
                                 <button class="push-action"  onclick="add_content_type('file_upload')">
                                    <span>File Upload</span>
                                </button>                          
                              </div>
                              <div class="col-xs-4">
                                  
                              </div>
                            </div>
                            <div class="back-button"><span onclick="back_to_add()"><i class="sprite_l_arrow mainsprite"></i> Back</span></div>
                            <div class="clearfix"></div>
                        </div><!-- end box content -->
                      </div><!-- end box -->
                    </div><!-- end col-md-12-->
                  </div>
        </div>
        
        <div id="adding-open">
<!--            <div class="col-md-12">
                <div class="box">
                  <div class="heading">
                      <div class="closable mainsprite sprite_close"><a href="#"></a></div>
                      <h2>How long is the answer?</h2>
                      <h3>Select an option</h3>
                  </div>
                  <div class="box-content one-row three-column">
                      <div class="row">
                        <div class="col-xs-4 button_online">              
                            <button class="push-action" onclick="add_content_type('one')">
                                <i class="mainsprite sprite_online"></i>
                                <span>One-Line</span>
                            </button>                
                        </div>
                        <div class="col-xs-4 button_short" onclick="add_content_type('short')">              
                             <button class="push-action">
                                <i class="mainsprite sprite_short"></i>
                                <span>Short</span>
                            </button>             
                        </div>
                        <div class="col-xs-4 button_long">              
                            <button class="push-action"  onclick="add_content_type('essay')">
                                <i class="mainsprite sprite_long"></i>
                                <span>Long Essay</span>
                            </button>                                    
                        </div>
                      </div>
                      <div class="back-button"><span onclick="add_question()"><i class="sprite_l_arrow mainsprite"></i> Back</span></div>
                      <div class="clearfix"></div>
                  </div> end box content 
                </div> end box 
              </div>-->
                <div class="row">
                        <div class="col-md-12">
                          <div class="box">
                            <div class="heading">
                                <div class="closable mainsprite sprite_close"><span onclick='end_new_page_element()'></span></div>
                                <h2>How long is the answer?</h2>
                                <h3>Select an option.</h3>
                            </div>
                            <div class="box-content one-row three-column">
                                <div class="row slide-left-fade-in">
                                  <div class="col-xs-4 button_online">              
                                      <button class="push-action" onclick="add_content_type('one')">
                                          <i class="mainsprite sprite_online"></i>
                                          <span>One-Line</span>
                                      </button>                
                                  </div>
                                  <div class="col-xs-4 button_short">              
                                       <button class="push-action" onclick="add_content_type('short')">      
                                          <i class="mainsprite sprite_short"></i>
                                          <span>Short</span>
                                      </button>             
                                  </div>
                                  <div class="col-xs-4 button_long">              
                                      <button class="push-action"  onclick="add_content_type('essay')">
                                          <i class="mainsprite sprite_long"></i>
                                          <span>Long Essay</span>
                                      </button>                                    
                                  </div>
                                </div>
                                <div class="back-button"><span onclick="add_question()"><i class="sprite_l_arrow mainsprite"></i> Back</span></div>
                                <div class="clearfix"></div>
                            </div><!-- end box content -->
                          </div><!-- end box -->
                        </div><!-- end col-md-12-->
                      </div>
        </div>
    </div>
    
    <div style='display:none'>
        <div class="progress" style='clear:both; display:block; margin-top:10px'>
            <div class="progress-bar progress-bar-success progress-bar-striped indicator" 
                 role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            </div>
        </div>
    </div>
    <script>
        onload_functions = ['prepopulate_deadline'];
    </script>
@stop