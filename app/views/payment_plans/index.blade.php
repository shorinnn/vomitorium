@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            
           @if($processors->count()==0)
            <div class="payment-processor">
                Choose your payment processor:<br />
                 <input type='radio' name='processor' value='Stripe' id='stripe' onclick='choose_processor(event)' /> <label for='stripe'>Stripe</label><br />
                 <input type='radio' name='processor' value='Paypal' id='paypal' onclick='choose_processor(event)' /> <label for='paypal'>Paypal</label><br />
                 <div class='well Stripe-details nodisplay'>
                     <input type='text' class='form-control' id='data-key' name='data-key' placeholder="Stripe Publishable Key"/><br />
                     <input type='text' class='form-control' id='secret-key' name='secret-key' placeholder="Stripe Secret Key"/><br />
                 </div>
                 <div class='well Paypal-details nodisplay'>
                     <input type='text' class='form-control' name='email' id='email' placeholder="Paypal Email Address" /><br />
                     <input type='text' class='form-control' name='confirmation_email' id='confirmation_email' placeholder="Paypal Email Address Confirmation" /><br />
                 </div>
                 <button onclick='save_processor(false)' class='btn btn-default'>Next</button>
             </div>
               <div class="payment-plan nodisplay">
                   Congratulations! You've set <span id='processor'></span> up!<br />
                   Create Your First Payment Plan<br />
                   {{View::make('payment_plans.create_plan')}}
               </div>
           <table class='table table-striped table-bordered nodisplay'>
                <thead>
                    <tr><th colspan="8">Payment Plans <button class='btn btn-primary pull-right' onclick='show_plan_modal()'>Create New Payment Plan</button></th></tr>
                    <tr><th>Name</th><th>Pricing</th><th>Group Conversations</th><th>Coach Conversations</th><th>No. Clients</th><th>Cancelled</th><th></th><th></th></tr>
                </thead>
                <tbody>
                    
                    @foreach($plans as $p)
                    {{View::make('payment_plans.plan')->withPlan($p)->withProcessor($processors()->first())}}
                    @endforeach
                </tbody>
            </table>
           
           @else
           <a href="payment_plans/processors">Manage Payment Processors</a>
            @if($plans->count()==0)
                 <div class="payment-plan">
                     Create Your First Payment Plan<br />
                     {{View::make('payment_plans.create_plan')}}
                 </div>
            
            <table class='table table-striped table-bordered nodisplay'>
            @else
            <table class='table table-striped table-bordered'>
            @endif
                <thead>
                    <tr><th colspan="8">Payment Plans <button class='btn btn-primary pull-right' onclick='show_plan_modal()'>Create New Payment Plan</button></th></tr>
                    <tr><th>Name</th><th>Pricing</th><th>Group Conversations</th><th>Coach Conversations</th><th>No. Clients</th><th>Cancelled</th><th></th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($plans as $p)
                        {{View::make('payment_plans.plan')->withPlan($p)->withProcessor($processors->first())}}
                    @endforeach
                </tbody>
            </table>
            <div class='hidden'>{{View::make('payment_plans.create_plan')}}</div>
           @endif
           
        </div>
        <!-- /.container -->

    </div>
<script>
    is_lesson_editor = true;
</script>
    <!-- /.section -->
@stop