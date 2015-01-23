@if($plan->type=='one-time')
    <?php
        $custom['u'] = Auth::user()->id;
        $custom['p'] = $plan->id;
        if(Session::has('trial')){
            $custom['t'] = 1;
            $unit = singplural(1, $plan->trial_duration_unit);
            $name = "$plan->trial_duration $unit TRIAL $$plan->trial_cost for $plan->name ($$plan->trial_cost)";
            $cost = $plan->trial_cost;
        }
        else{
            $name = $plan->name;
            $cost = $plan->cost;
        }
        $custom = urlencode(json_encode($custom));
    ?>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="{{$processor->field}}">
    <input type="hidden" name="lc" value="US">
    <input type="hidden" name="item_name" value="{{$name}}">
    <input type="hidden" name="amount" value="{{$cost}}">
    <input type="hidden" name="currency_code" value="{{$plan->currency}}">
    <input type="hidden" name="button_subtype" value="services">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="return" value="{{url('/thank_you')}}">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="custom" value="{{$custom}}">
    <input type="hidden" name="notify_url" value="{{url('paypal_ipn')}}">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
@else
    <?php
        $subscribed_before = DB::table('programs_users')->where('user_id', Auth::user()->id)->where('subscription_id', $plan->id)->count();
        $custom['u'] = Auth::user()->id;
        $custom['p'] = $plan->id;
        $custom = urlencode(json_encode($custom));
    ?>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="business" value="{{$processor->field}}">
        <input type="hidden" name="cmd" value="_xclick-subscriptions">
        <input type="hidden" name="item_name" value="{{$plan->name}}">
        <input type="hidden" name="currency_code" value="{{$plan->currency}}">
        @if($plan->trial_duration>0 && !$subscribed_before)
            <input type="hidden" name="a1" value="{{$plan->trial_cost}}">
            <input type="hidden" name="p1" value="{{$plan->trial_duration}}">
            <input type="hidden" name="t1" value="{{strtoupper(substr($plan->trial_duration_unit,0,1))}}">
        @endif
        <input type="hidden" name="a3" value="{{$plan->cost}}">
        <input type="hidden" name="p3" value="{{$plan->subscription_duration}}">
        <input type="hidden" name="t3" value="{{strtoupper(substr($plan->subscription_duration_unit,0,1))}}">
        <input type="hidden" name="src" value="1">
        <input type="hidden" name="return" value="{{url('/thank_you')}}">
        <input type="hidden" name="rm" value="2">
        <input type="hidden" name="custom" value="{{$custom}}">
        <input type="hidden" name="notify_url" value="{{url('paypal_ipn')}}">
        <input type="image" name="submit" border="0"
        src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif"
        alt="PayPal - The safer, easier way to pay online">
        <img alt="" border="0" width="1" height="1"
        src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >
    </form>
@endif