@extends($layout)

@section('content')

        <div class="container">
            <h3>
                <a href='{{url('lessons')}}'><i class='glyphicon glyphicon-chevron-left'></i> Back to Lesson Manager</a>
                <a id='public_view_link' href="{{url("lessons/view_lesson/".$data['lesson']->id)}}"  target="_blank" class='btn btn-primary'><i class="glyphicon glyphicon-eye-open"></i> Public view</a>
            </h3>
            
            <h1>Lesson Editor</h1>
            <table class="table">
                <tr><td>Title</td>
                    <td><a class="editable" href="#" id="title" data-type="text" data-pk="{{$data['lesson']->id}}" 
                       data-name="title" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter Title" data-mode='inline'>{{$data['lesson']->title}}
                      </a>
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
                <tr class='advanced_lesson'>
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
                </tr>
                <tr class='advanced_lesson'>
                    <td>Blocks Per Section (If sections are used)</td>
                    <td><a class="editable" href="#" id="section_capacity" data-type="text" data-pk="{{$data['lesson']->id}}" 
                       data-name="section_capacity" data-url="{{action("LessonsController@update", array($data['lesson']->chapter_id, $data['lesson']->id))}}" data-original-title="Enter Blocks Per Section" data-mode='inline'>{{$data['lesson']->section_capacity}}
                      </a>
                    </td>
                </tr>
                </div>
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
                    @else
                        {{ View::make('lessons.question_block')->withBlock($block) }}
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
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
    <script>
        var is_lesson_editor = true;
    </script>
    <div style="display:none">
            <div id="adding-step-1">
                <div class="col-md-12">
                    <div class="box">
                        <div class="heading"><div class="closable mainsprite sprite_close"><span onclick="end_new_page_element()"></span></div>
                            <h2>What do you want to add ?</h2>               
                            <h3>Select an option</h3>            
                        </div>
                        <div class="box-content one-row two-column">
                            <div class="row">
                                <div class="col-xs-6 button_content"> 
                                    <button class="push-action" onclick="add_content()"><i class="mainsprite sprite_content"></i><span>Content</span> </button>
                                </div>
                                <div class="col-xs-6 button_question">
                                    <button class="push-action" onclick="add_question()"><i class="mainsprite sprite_question"></i><span>Question</span></button>       
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
       
        
        <div id="adding-question">
            <div class="col-md-12">
          <div class="box">
             <div class="heading">
                        <div class="closable mainsprite sprite_close"><span onclick="end_new_page_element()"></span></div>
                        <h2>What do you want to add ?</h2>
                        <h3>Select a question type</h3>
                    </div>
            <div class="box-content two-row three-column notpadd">
                <div class="row">
                  <div class="col-md-12">
                    <div class="center">  &nbsp;</div>
                  </div>                  
                </div>
                <div class="row">
                  <div class="col-xs-4 button_texthtml">               
                    <button class="push-action" onclick="add_open_type()">
                        <i class="mainsprite sprite_open_ended"></i>
                        <span>Open Ended</span>
                    </button> 
                  </div>
                  <div class="col-xs-4 button_video">               
                     <button class="push-action" onclick="add_content_type('scale')">
                        <i class="mainsprite sprite_1scale"></i>
                        <span>Scale</span>
                    </button> 
                 </div>
                  <div class="col-xs-4 button_filed">                           
                    <button class="push-action" onclick="add_content_type('skill-select')">
                    ???
                    <span>Skill Select</span>
                </button>          
                 </div>
                </div> 
                <div class="row">
                  <div class="col-md-12">
                    <div class="center"> &nbsp;</div>
                  </div>                  
                </div> 
                <div class="row">
                  <div class="col-xs-4 button_sans">    
                    <button class="push-action" onclick="add_content_type('mc')">
                                        <i class="mainsprite sprite_multiple"></i>
                                        <span>Multiple Choice</span>
                                    </button>  
                  </div>
                  <div class="col-xs-4 button_cond">                               
                     <button class="push-action"  onclick="add_content_type('sortable')">
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
        </div>
        </div>
        
        <div id="adding-content">
            <div class="col-md-12">
          <div class="box">
            <div class="heading">
                <div class="closable mainsprite sprite_close"><span onclick="end_new_page_element()"></span></div>
                <h2>Add content?</h2>
                <h3>Choose the type of content</h3>
            </div>
            <div class="box-content two-row three-column notpadd">
                <div class="row">
                  <div class="col-md-12">
                    <div class="center">Basic</div>
                  </div>                  
                </div>
                <div class="row">
                  <div class="col-xs-4 button_texthtml">               
                    <button class="push-action"  onclick="add_content_type('text')">
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
                  <div class="col-xs-4 button_filed"  onclick="add_content_type('file')">                           
                    <button class="push-action">
                        <i class="mainsprite sprite_filed"></i>
                        <span>File Download</span>
                    </button>             
                 </div>
                </div> 
                <div class="row">
                  <div class="col-md-12">
                    <div class="center">Advanced</div>
                  </div>                  
                </div> 
                <div class="row">
                  <div class="col-xs-4 button_sans">    
                    <button class="push-action tooltipx tooltipstered"  onclick="add_content_type('sortable_skills')">
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
                      <button class="btn btn-primary" onclick="add_content_type('top_skills')">Top Skills?</button>
                  </div>
                </div>
                <div class="back-button"><span onclick="back_to_add()"><i class="sprite_l_arrow mainsprite"></i> Back</span></div>
                <div class="clearfix"></div>
            </div><!-- end box content -->
          </div><!-- end box -->
        </div>
        </div>
        
        <div id="adding-open">
            <div class="col-md-12">
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
                  </div><!-- end box content -->
                </div><!-- end box -->
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
@stop