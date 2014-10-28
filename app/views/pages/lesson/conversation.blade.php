@extends('layouts.conversations_layout')

@section('content')
<div class="section lesson-content">
        <div class="containerdeprecated">
            <div id="posted_remarks">
                @if($remarks->count()>0)
                <p class="green-bg conversations-title">Conversation</p>
                <div class='remarks-container'>
                    {{ View::make('pages.lesson.remarks')->withRemarks($remarks) }}
                </div>
                    @if(!admin())
                        @if($remarks[$remarks->count()-1]->posted_by == 'user')
                            <button class='btn btn-default' onclick='force_edit("#posted_remarks")'>Edit</button>
                        @else
                        <span id='remark-reply-area'></span>
                        <textarea  id="remark_reply" class="white-textarea summernote_editor"></textarea>
                        
                        <button type="button" class="btn btn-default2 message-send" 
                                data-rte='#remark_reply' 
                                data-container='.remarks-container' onclick="do_remark_reply(event,{{$lesson->id}})">Send</button>
                        <ul class="list-unstyled option-box-2">
                            <li><a href="#" data-toggle="tooltip" title="" 
                                   data-rte='#remark_reply' data-input='attachment'
                                   data-original-title="Attach" class="do-tooltip icon-2" onclick="attach(event)"></a></li>
                            <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" data-target='#remark_reply' onclick="discard(event)" class="do-tooltip icon-3"></a></li>
                        </ul>
                        @endif
                    @endif
                
                @endif
            </div>
            @if(admin())
                @if($remarks[$remarks->count()-1]->posted_by == 'admin')
                        <button class='btn btn-default' onclick='force_edit("#posted_remarks")'>Edit</button>
                @else
                    <div>
                        <h4 class="leave_remarks">
                            @if($remarks->count()>0)
                            Reply
                            @else
                            Start the conversation
                            @endif
                        </h4>
                        <input type='hidden' name="user" id="remark_user" value='{{Session::get('user_id')}}' />
                        <input type='hidden' name="lesson" id="remark_lesson" value='{{$lesson->id}}' />
                        <textarea id="remark" class="form-control white-textarea summernote_editor"></textarea><br />
                        <button id="post_remark_btn" type="button" class="btn btn-default2 message-send" 
                                data-rte='#remark' data-container='#posted_remarks'
                                onclick="post_coach_remarks(event,'{{url('post_remark')}}')">Send</button>
                        <ul class="list-unstyled option-box-2">
                            <li><a href="#" data-toggle="tooltip" title="" data-original-title="Attach" class="do-tooltip icon-2" 
                                   data-rte='#remark' data-input='attachment' 
                                   onclick="attach(event)"></a></li>
                            <li><a href="#" data-toggle="tooltip" title="" data-original-title="Discard" class="do-tooltip icon-3" data-target='#remark' onclick="discard(event)"></a></li>
                        </ul>
                        <br />
                        <br />
                    </div>
                @endif
            @endif
        </div>
    
   
        <!-- /.container -->
    </div>
<div id='hidden_assets' style='display:none'>
        <input type="file" name="attachment" id="attachment" />
        <input type="hidden" name="attachments" id="attachments" value="[]" />
        <input type="hidden" name="comment_attachments" id="comment_attachments" value="[]" />
          <div class="progress" style='clear:both; display:block; margin-top:10px'>
          <div class="progress-bar progress-bar-success progress-bar-striped indicator" 
               role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
          </div>
        </div>
    </div>
<script>
        var do_enable_rte = true;
        var rte_config = 3;
    </script>
@stop