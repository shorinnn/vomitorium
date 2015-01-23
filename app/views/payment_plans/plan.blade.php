<tr>
    <td>{{$plan->name}}</td>
    <td>{{currency_symbol($plan->currency)}} {{$plan->cost}}
        @if($plan->type=='subscription')
        for {{$plan->subscription_duration}} 
        {{singplural($plan->subscription_duration, $plan->subscription_duration_unit )}}
        @endif
        
        @if($plan->trial_duration > 0)
        <br />{{$plan->trial_duration}} {{ singplural(1, $plan->trial_duration_unit) }} 
         trial for {{currency_symbol($plan->currency)}} {{$plan->trial_cost}}
        @endif
    </td>
    <td>{{$plan->clients()}}</td>
    <td>{{$plan->cancelled()}}</td>
    <td>
        <?php
        $name = sys_settings('title')=='' ? sys_settings('domain') : sys_settings('title');
        if($processor->name=='Stripe'){
            //$code = View::make('payment_plans.stripe_code')->withPlan($plan)->withName($name)->withProcessor($processor)->render();
            $code = url('register').'/'.sha1(sys_settings().$plan->id);
            $trial_code = url('register').'/'.sha1(sys_settings().$plan->id).'-trial';
        }
        else{
            $code = 'paypal';
            $code = url('register').'/'.sha1(sys_settings().$plan->id);
            $trial_code = url('register').'/'.sha1(sys_settings().$plan->id).'-trial';
        }
        $code = str_replace("'", '&#8217;', $code);
        ?>
        <button class='btn btn-primary btn-sm' 
                data-code='{{{$code}}}' onclick='get_buy_button(event)'>Get Buy Link</button>
        @if($plan->type=='one-time' && $plan->trial_duration>0)
        <button class='btn  btn-primary btn-sm' 
                data-code='{{{$trial_code}}}' onclick='get_buy_button(event)'>Get Trial Link</button>
        @endif
    </td>
</tr>