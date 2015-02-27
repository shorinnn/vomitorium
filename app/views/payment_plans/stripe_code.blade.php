<form action="{{url('purchase')}}" method="POST">
    <center>
      <script
        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
        data-label='Pay With Credit Card'
        data-currency='{{$plan->currency}}'
        @if( $plan->type=='subscription' )
            data-panelLabel = 'Subscribe'
        @endif
        
        @if(!Session::has('trial'))
            data-key="{{$processor->field}}"
            data-amount="{{$plan->cost*100}}"
            data-name="{{$plan->name}}"
            @if($plan->trial_duration == 0)
                data-description="{{$plan->name}} ({{currency_symbol($plan->currency)}} {{$plan->cost}})">
            @else
                data-description="{{currency_symbol($plan->currency)}}{{$plan->trial_cost}} Trial,
                then {{currency_symbol($plan->currency)}}{{$plan->cost}}">
            @endif
            </script>
            <input type="hidden" name="amount" value="{{$plan->cost*100}}" />
        @else
            data-key="{{$processor->field}}"
            data-amount="{{$plan->trial_cost*100}}"
            data-name="{{$plan->name}}"
            data-description="{{$plan->name}} Trial ({{currency_symbol($plan->currency)}} {{$plan->trial_cost}})">
            </script>
            <input type="hidden" name="amount" value="{{$plan->trial_cost*100}}" />
        @endif

        <input type="hidden" name="program" value="{{$plan->program_id}}" />
        <input type="hidden" name="plan_id" value="{{$plan->id}}" />
    </center>
</form>