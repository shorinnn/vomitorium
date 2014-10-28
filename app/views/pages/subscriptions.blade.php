@extends($layout)
@section('content')
 <!-- Page Content -->

    <div class="section">
        <div class="container">
            @if(count($subscriptions)==0)
                You have no subscriptions.
            @else
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Payment plan</th>
                            <th>Program</th>
                            <th>Payment type</th>
                            <th>Expires</th>
                            <th>Cancel</th>
                        </tr>
                    </thead>
                    @foreach($subscriptions as $s)
                    <?php $plan = PaymentPlan::find($s->subscription_id);?>
                    <tr class='row-{{$s->stripe_subscription_id}}'>
                        <td>{{$plan->name}}</td>
                        <td>{{$plan->program->name}}</td>
                        <td>
                            @if($plan->type=='one-time')
                                One time
                            @else
                                Recurring
                            @endif
                        </td>
                        <td>
                            @if($plan->type=='one-time')
                                Never
                            @else
                                <span title='{{format_date($s->expires)}}' class='do-tooltip'>
                                    {{\Carbon\Carbon::createFromTimeStamp(strtotime($s->expires))->diffForHumans() }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($plan->type=='subscription')
                                <button class="btn btn-primary btn-sm" onclick='cancel_subscription("{{$s->stripe_subscription_id}}")'>Cancel Subscription</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
@stop