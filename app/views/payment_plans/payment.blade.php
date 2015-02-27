@extends('layouts.payment')

@section('content')
<div class="section">
    <?php
    $stripe = PaymentProcessor::where('name','Stripe')->first();
    $paypal = PaymentProcessor::where('name','Paypal')->first();
    $name = sys_settings('title')=='' ? sys_settings('domain') : sys_settings('title');
    ?>
        @foreach($plans as $p)
        @if($plans->count()>1)
            <div class='well col-lg-5'>
        @else
            <div class="col-lg-3"></div>
            <div class='well col-lg-6'>
        @endif
                @if(!Session::has('trial'))
                    <h2 class='text-center'>{{$p->name}}</h2>
                    <p class='text-center' style='color:green'>{{currency_symbol($p->currency)}}{{$p->cost}}
                    @if($p->type=='subscription')
                        for {{$p->subscription_duration}} 
                        {{singplural($p->subscription_duration, $p->subscription_duration_unit )}}
                        @if($p->trial_duration>0)
                            <br />Including {{$p->trial_duration}} {{singplural(1,$p->trial_duration_unit)}} trial 
                            ({{currency_symbol($p->currency)}}{{$p->trial_cost}})
                        @endif
                    @endif
                    </p>
                @else
                    <h2 class='text-center'>{{$p->name}} -  {{$p->trial_duration}} {{singplural(1,$p->trial_duration_unit)}} Trial</h2>
                    <p class='text-center' style='color:green'>{{currency_symbol($p->currency)}} {{$p->trial_cost}}</p>
                @endif
            <br />
            <br />
            @if($stripe!=null)
                @if($paypal!=null)
                    <div class="col-lg-6">
                @else
                    <div>
                @endif
                    {{View::make('payment_plans.stripe_code')->withPlan($p)->withName($name)->withProcessor($stripe)->render()}}
                </div>
            @endif
            
            @if($paypal!=null)
                @if($stripe!=null)
                    <div class="col-lg-6">
                @else
                    <div>
                @endif
                    {{View::make('payment_plans.paypal_code')->withPlan($p)->withName($name)->withProcessor($paypal)->render()}}
                </div>
            @endif
            </div>
        <div class='col-lg-1'></div>
        @endforeach

     @if($plans->count()>1)
                    </div>
        @else
            
            </div>
            <div class="col-lg-3"></div>
        @endif
    <!-- /.section -->
    
@stop