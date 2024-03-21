@foreach($datas as $data)
<tr id="order-bulk-delete">
  <td><input type="checkbox" class="bulk-item" value="{{$data->id}}"></td>

    <td>
        {{ $data->id}}

    </td>
    
    <td>
        {{ json_decode($data->billing_info,true)['bill_first_name']}}
    </td>
    <td>
      {{ $data->user->email}}
    </td>
    <td>
      {{ $data->user->phone}}
    </td>
    <td>
      @if ($setting->currency_direction == 1)
      {{$data->currency_sign}}{{PriceHelper::OrderTotal($data)}}
      @else
      {{PriceHelper::OrderTotal($data)}}{{$data->currency_sign}}
      @endif
    </td>

    <td>
        <div class="dropdown">
            <button class="btn btn-{{ $data->payment_status == 'Paid' ?  'success': 'danger' }} btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ $data->payment_status == 'Paid' ?  __('Paid') : __('Unpaid')  }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;" data-href="{{ route('back.order.status',[$data->id,'payment_status','Paid']) }}">{{ __('Paid') }}</a>
              <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;" data-href="{{ route('back.order.status',[$data->id,'payment_status','Unpaid']) }}">{{ __('Unpaid') }}</a>
            </div>
          </div>
    </td>
    <td>
      @if ($data->refill_order[0]->value == true)
        <div class="circle-green"></div>
      @else
        <div class="circle-red"></div>
      @endif
      
    </td>
    <td>
        <div class="dropdown">
            <button class="btn {{ $data->order_status  }}  btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ $data->order_status  }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;" data-href="{{ route('back.order.status',[$data->id,'order_status','Pending']) }}">{{ __('Pending') }}</a>
              <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;" data-href="{{ route('back.order.status',[$data->id,'order_status','In Progress']) }}">{{ __('In Progress') }}</a>
              <a class="dropdown-item ship-link" data-toggle="modal" data-target="#statusshipModal" href="javascript:;"  data-href="{{ route('back.order.status_livraison') }}"   data-id="{{ $data->id }}" data-order-status="Delivered">{{ __('Shipped') }}</a>
              <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;" data-href="{{ route('back.order.status',[$data->id,'order_status','Canceled']) }}">{{ __('Canceled') }}</a>
              <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;" data-href="{{ route('back.order.status',[$data->id,'order_status','Denied']) }}">{{ __('Denied') }}</a>
            </div>
          </div>
    </td>
    <td>
        <div class="action-list">
            <a class="btn btn-secondary btn-sm"
                href="{{ route('back.order.invoice',$data->id) }}">
                <i class="fas fa-eye"></i>
            </a>
            <a class="btn btn-danger btn-sm " data-toggle="modal"
                data-target="#confirm-delete" href="javascript:;"
                data-href="{{ route('back.order.delete',$data->id) }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
