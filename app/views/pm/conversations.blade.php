@if(get_class($convo)=='Illuminate\Pagination\Paginator')
    @if(Input::has('filter'))
        {{$convo->appends(array('filter'=>Input::get('filter')))->links()}}
    @else
        {{$convo->links()}}
    @endif
@endif

<div class="panel-group accordion" 
    @if(get_class($convo)=='Illuminate\Pagination\Paginator')
        id="accordion"
    @else
        id="search_accordion"
    @endif
     >              
                @foreach($convo as $c)
                <?php
                $sender = admin() ?  $c->user->first_name.' '.$c->user->last_name : $c->admin->first_name.' '.$c->admin->last_name;
                ?>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a data-toggle="collapse" 
                            @if(get_class($convo)=='Illuminate\Pagination\Paginator')
                                data-parent="#accordion" href="#collapse-{{$c->id}}" 
                            @else
                                data-parent="#search_accordion" href="#search-collapse-{{$c->id}}" 
                            @endif
                            @if((admin() && $c->attended==0) || (!admin() && $c->read==0))  
                            style='font-weight:bold'
                            @endif
                            class="collapsed">
                            <div class="row">
                                <div class="col-lg-3">
                                @if($c->is_pm==1) 
                                <i class='glyphicon glyphicon-envelope do-tooltip' title="Private Message" style='color:#428BCA'></i> {{$sender}}
                                </div> <div class="col-lg-7">
                                @else
                                    @if($c->lesson_id>0) 
                                        <i class='glyphicon glyphicon-list-alt do-tooltip' titl="Lesson" style='color:#028900'></i> {{$sender}} 
                                        </div><div class="col-lg-7"> Chapter {{$c->lesson->chapter->title}} - {{$c->lesson->title}} -
                                    @else 
                                        <i class='glyphicon glyphicon-question-sign do-tooltip' title="Question" style='color:#FC6E00'></i> {{$sender}} 
                                        </div><div class="col-lg-7">{{$c->block_answer()->block->title}} -
                                    @endif
                                @endif

                                {{{Str::limit(strip_tags($c->content), 30)}}}  
                                </div>
                                <div class="col-lg-2">
                                  <span class="time pull-right do-tooltip" title='{{format_date($c->created_at)}}'> 
                                    {{$c->created_at->diffForHumans()}}</span>
                                </div>
                              </div>
                          </a>
                        </h4>
                      </div>
                      <div 
                          @if(get_class($convo)=='Illuminate\Pagination\Paginator')
                              id="collapse-{{$c->id}}" 
                          @else
                              id="search-collapse-{{$c->id}}" 
                          @endif
                          data-convo='{{$c->id}}' class="panel-collapse collapse">
                        <div class="panel-body">
                            Loading...
                        </div>
                      </div>
                    </div>
                @endforeach
                </div>
                 <br />
                 <div class='row'><div class='col-lg-11'>
                @if(get_class($convo)=='Illuminate\Pagination\Paginator')
                    @if(Input::has('filter'))
                        {{$convo->appends(array('filter'=>Input::get('filter')))->links()}}
                    @else
                        {{$convo->links()}}
                    @endif
                @endif
                     </div></div>