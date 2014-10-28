@extends($layout)

@section('content')
<div class="section">

        <div class="container">
            <button class='btn btn-default' onclick="slideToggle('.payment-processor')">Add New Processor</button>
            <div class="payment-processor" style="display:none">
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
                 <button onclick='save_processor(true)' class='btn btn-default'>Next</button>
             </div>
            
            <br />
            <table class='table table-striped table-bordered'>
                <thead>
                    <tr><th>Name</th><th>API Details</th><th>Remove</th></tr>
                </thead>
                <tbody>
                    @foreach($processors as $p)
                        {{View::make('payment_plans.processor')->withP($p)}}
                    @endforeach
                </tbody>
            </table>
           
            
            
            
        </div>
        <!-- /.container -->

    </div>
    <!-- /.section -->
@stop