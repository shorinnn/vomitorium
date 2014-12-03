<div class="text-center">
    <img src='{{url('assets/img/greensuccess.png')}}' />
    <br />
    <h3 class='green'>Success!</h3>
    <span class='green'>Registration email sent to</span> <a href="mailto:{{$email}}">{{$email}}</a>
    <br />
    <br />
    <button class='btn btn-default' onclick="add_client_modal(0,'#send_codes')">Send Another Access Pass</button>
</div>