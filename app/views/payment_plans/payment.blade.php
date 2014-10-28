@extends('layouts.payment')

@section('content')
<div class="section">
    <?php
    $stripe = PaymentProcessor::where('name','Stripe')->first();
    $paypal = PaymentProcessor::where('name','Paypal')->first();
    $name = sys_settings('title')=='' ? sys_settings('domain') : sys_settings('title');
    ?>
        @foreach($plans as $p)
            <div class='well col-lg-5'>
                @if(!Session::has('trial'))
                    {{$p->name}}
                    ${{$p->cost}}
                    @if($p->type=='subscription')
                        for {{$p->subscription_duration}} 
                        {{singplural($p->subscription_duration, $p->subscription_duration_unit )}}
                        @if($p->trial_duration>0)
                            <br />Including {{$p->trial_duration}} {{singplural(1,$p->trial_duration_unit)}} trial (${{$p->trial_cost}})
                        @endif
                    @endif
                @else
                {{$p->name}} -  {{$p->trial_duration}} {{singplural(1,$p->trial_duration_unit)}} Trial
                    ${{$p->trial_cost}}
                @endif
            <br />
            <br />
            @if($stripe!=null)
                <div class="col-lg-6">
                    {{View::make('payment_plans.stripe_code')->withPlan($p)->withName($name)->withProcessor($stripe)->render()}}
                </div>
            @endif
            @if($paypal!=null)
                <div class="col-lg-6">
                    {{View::make('payment_plans.paypal_code')->withPlan($p)->withName($name)->withProcessor($paypal)->render()}}
                </div>
            @endif
            </div>
        <div class='col-lg-1'></div>
        @endforeach

    </div>
    <!-- /.section -->
    
@stop