<div class="block_div" id='block-{{$block->id}}'>
    <?php
    if($block->subtitle==''){
        $cats = DB::table('block_categories')->get();
    }
    else{
        $checked_arr = array();
        try{
            if($block->subtitle!='') $checked_arr = json_decode($block->subtitle);
        }
        catch(Exception $e){
            $checked_arr = array();
        }
        $cats = DB::table('block_categories')->whereIn('id', $checked_arr)->get();
    }?>
    @foreach($cats as $c)
        <input type="radio" name="c{{$block->id}}" value="{{$c->id}}" id="c{{$block->id}}-{{$c->id}}" onclick="load_dynamic_answers({{$c->id}},{{$block->id}})"/> 
        <label for="c{{$block->id}}-{{$c->id}}">{{$c->category}}</label><br />
    @endforeach
    <div id="ajax-{{$block->id}}"></div>
</div>
<img src="{{url('/assets/img/ajax-loader.gif')}}" class="hidden" />