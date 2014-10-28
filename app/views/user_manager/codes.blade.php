<div class="text-center">
    <img src='{{url('assets/img/greensuccess.png')}}' />
    <br />
    <h3 class='green'>Success!</h3>
    <span class='green'>{{count($codes)}} {{singplural(count($codes),'codes')}} generated.</span><br /><br />
Copy and paste these somewhere.<br />
However, you can track them in <a href='{{url('users/codes')}}'>codes</a> for your convenience.<br /><br />
    @foreach($codes as $c)
    <p class='green'>
        <input type='text'  class='form-control selectable-txt pull-left copy-source-{{$c->id}} copy-to'
               style="width:93%" value='{{url("register/$c->code")}}' data-clipboard-text="{{url("register/$c->code")}}" 
         data-id='{{$c->id}}'
                />
    <img src="{{url('assets/img/clipboard.png')}}" class='copy-to' data-clipboard-text="{{url("register/$c->code")}}" 
         data-id='{{$c->id}}' height="32" />
    
    
        <span class='clearfix clear_fix'></span>
    </p>
    @endforeach
<br />
    <button class='btn btn-default' onclick="add_client_modal(0,'#register_codes')">Generate More Codes</button>
</div>