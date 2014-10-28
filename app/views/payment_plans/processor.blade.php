<tr class='row-{{$p->id}}'>
    <td>{{$p->name}}</td>
    <td>
        @if($p->name=='Stripe')
        <div class='col-lg-4'>
            Publishable Key 
        </div>
        <div class='col-lg-8'>
            <a class="editable editable-click" href="#" id="field"
               data-type="text" data-pk="{{$p->id}}" 
               data-url="{{url('payment_plans/update_processor')}}" data-mode="inline">{{$p->field}}</a>
        </div>
        <div class='col-lg-4'>
            Secret Key 
        </div>
        <div class='col-lg-8'>
            <a class="editable editable-click" href="#" id="field2"
               data-type="text" data-pk="{{$p->id}}"
               data-url="{{url('payment_plans/update_processor')}}"  data-mode="inline">{{$p->field2}}</a>
        </div>
        @else
        <div class='col-lg-4'>
            Paypal Email Address
        </div>
        <div class='col-lg-8'>
            <a class="editable editable-click" href="#" id="field"
               data-type="text" data-pk="{{$p->id}}"
               data-url="{{url('payment_plans/update_processor')}}"  data-mode="inline">{{$p->field}}</a>
        </div>
        @endif
    </td>
    <td class='text-right' style='width:200px;'>
        <button class='btn btn-danger btn-warning delete-btn do-tooltip'  title='Delete this payment processor'
                data-target='row' data-id='{{$p->id}}' data-url="{{url("payment_plans/delete_processor", $p->id)}}">
            <i class='glyphicon glyphicon-trash'></i></button>
    </td>