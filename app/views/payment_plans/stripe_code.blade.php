<form action="{{url('purchase')}}" method="POST">
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    @if(!Session::has('trial'))
        data-key="{{$processor->field}}"
        data-amount="{{$plan->cost*100}}"
        data-name="{{$name}}"
        data-description="{{$plan->name}} (${{$plan->cost}})">
        </script>
        <input type="hidden" name="amount" value="{{$plan->cost*100}}" />
    @else
          data-key="{{$processor->field}}"
        data-amount="{{$plan->trial_cost*100}}"
        data-name="{{$name}}"
        data-description="{{$plan->name}} Trial (${{$plan->trial_cost}})">
        </script>
        <input type="hidden" name="amount" value="{{$plan->trial_cost*100}}" />
    @endif
  
    <input type="hidden" name="program" value="{{$plan->program_id}}" />
    <input type="hidden" name="plan_id" value="{{$plan->id}}" />
</form>