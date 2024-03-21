@extends('master.front')
@section('title')
    {{__('Payment')}}
@endsection
@section('content')
    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="column">
        <ul class="breadcrumbs">
          <li><a href="{{route('front.index')}}">{{ __('Home') }}</a> </li>
          <li class="separator"></li>
          <li>{{ __('Review your order and pay') }}</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1  checkut-page">
    <div class="row">
      <!-- Payment Methode-->
      <div class="col-xl-9 col-lg-8">
        <div class="steps flex-sm-nowrap mb-5">
          {{-- <a class="step" href="{{route('front.checkout.billing')}}">
            <h4 class="step-title"><i class="icon-check-circle"></i>1. {{__('Invoice to')}}:</h4>
          </a> 
          <a class="step active" href="{{route('front.checkout.payment')}}">
            <h4 class="step-title">2. {{__('Review and pay')}}</h4>
          </a>
          <a class="step" href="">
            <h4 class="step-title">3. {{__('Ship to')}}:</h4>
          </a> --}}

          <a class="step " href="{{ route('front.checkout.billing') }}">
              <h4 class="step-title"><i class="icon-check-circle"></i>1. {{ __('Billing Address') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title"><i class="icon-check-circle"></i>2. {{ __('Delivery Adress') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title"><i class="icon-check-circle"></i>3. {{ __('Doctor Information') }}:</h4>
          </a>
          <a class="step active" href="">
              <h4 class="step-title">4. {{ __('Review and pay') }}</h4>
          </a>
          <a class="step" href="">
            <h4 class="step-title">5. {{ __('Shipping') }}</h4>
          </a>

        </div>
        <div class="card">
            <div class="card-body">
                <h6 class="pb-2">{{__('Review Your Order')}} :</h6>
        <hr>
        <div class="row padding-top-1x  mb-4">
          <div class="col-sm-6 cards-details">
            {{-- <h6>{{__('Invoice address')}} :</h6> --}}
            <h6>{{__('Patient Information')}} :</h6>
            @php
                $ship = Session::get('shipping_address');
                
                $bill = Session::get('billing_address');
                // var_dump($ship);
            @endphp

          <ul class="list-unstyled ">
            <li class="patient-information detail-bulling"><span class="detail-color">{{__('Name')}}: </span>{{$user['first_name']}} {{$user['last_name']}}</li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Gender')}}: </span>{{$user['gender']}} </li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Birthday')}}: </span>{{$user['date_birth']}} </li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Email')}}: </span>{{$user['email']}}</li>
              @if (PriceHelper::CheckDigital())
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Address')}}: </span>{{$ship['ship_address1']}}</li>
              @endif
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Phone')}}: </span>{{$ship['ship_phone']}}</li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Know Drug Allergie')}}: </span>{{$ship['drug_allergie']}}</li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Current medical condition')}}: </span>{{$ship['doctor_current_medical']}}</li>
            </ul>
          </div>
          <div class="col-sm-6 cards-details">
            <h6>{{__('Doctor Information')}} :</h6>
            <ul class="list-unstyled">
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Name')}}: </span>{{$bill['doctor_name']}} </li>
              @if (PriceHelper::CheckDigital())
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Address')}}: </span>{{$ship['doctor_address']}}</li>
              @endif
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Phone')}}: </span>{{$bill['doctor_phone']}}</li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('City')}}: </span>{{$bill['doctor_city']}}</li>
              <li class="patient-information detail-bulling"><span class="detail-color">{{__('Country')}}: </span>{{$bill['doctor_country']}}</li>
            </ul>

            @if (DB::table('states')->whereStatus(1)->count() > 0)
            <select name="state_id" class="form-control" id="state_id_select" required>
              <option value="" selected disabled>{{__('Select Shipping State')}}</option>
              @foreach (DB::table('states')->whereStatus(1)->get() as $state)
                  <option value="{{$state->id}}" data-href="{{route('front.state.setup',$state->id)}}" {{Auth::check() && Auth::user()->state_id == $state->id ? 'selected' : ''}} >{{$state->name}}
                      @if ($state->type == 'fixed')
                      ({{PriceHelper::setCurrencyPrice($state->price)}})
                      @else
                      ({{$state->price}}%)
                      @endif

                    </option>
              @endforeach
            </select>
            <small class="text-primary">{{__('please select shipping state')}}</small>
            @error('state_id')
                <p class="text-danger">{{$message}}</p>
            @enderror
            @endif
          </div>
        </div>

        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input  type="checkbox" id="email_check" >
            <label class="custom-control-label" for="email_check"> Do you want to receive an Alert for Refill via Mail ? </label>
          </div>
        </div>

        <h6>{{__('Pay with')}} : </h6>
        <div class="row mt-4">
          <div class="col-12">
            <div class="payment-methods">
              @php
                  $gateways = DB::table('payment_settings')->whereStatus(1)->get();
              @endphp

              <div class="single-payment-method">
                <div class="col-sm-12">
                  <button class="btn btn-success btn-block btn-lg mt-2" style="background: #0d6efd!important" type="submit" data-bs-toggle="modal" data-bs-target="#paypal">
                    <span> <i class="icon-mail"></i> {{__('Email')}}</span>
                  </button>
                </div>
              </div>

              <div class="single-payment-method">
                <div class="col-sm-12">
                  <button class="btn btn-primary btn-block btn-lg mt-2" style="background: #28a745!important">
                    <span> <i class="icon-phone"></i> {{__('Fax')}}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        </div>
        </div>


        <div class="modal fade" id="paypal" tabindex="-1"  aria-hidden="true">
          <form class="interactive-credit-card row" action="{{route('front.checkout.mail_admin')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">{{__('Transactions par email')}}</h6>
                        <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="col-lg-12">
                              
                              {{-- <div class="form-group">
                                <label for="name">{{ __('Prescription') }}</label>
                                <div class="col-lg-12 pb-1">
                                  <img id="previewImage" class="client-img"
                                    src=""
                                    alt="No Image Found">
                                </div>
                                <span>{{ __('Image Size Should Be 40 x 40.') }}</span>
                              </div> --}}

        
                              <div class="form-group">
                                <label for="name">{{ __(' Please upload your prescription') }} *</label>
                                <label class="file">
                                  <input type="file" required  accept="image/*,application/pdf"  class="upload-photo" name="photo" id="file" aria-label="File browser example" onchange="previewFile()">
                                  <span class="file-custom text-left">{{ __('Upload Image or pdf...') }}</span>
                                </label>  
                              </div>

                              <div class="form-group">
                                <p id="file-name"></p>
                              </div>

                              <input type="hidden" name="data_information" value="{{ json_encode($user) }}">
                              <input type="hidden" name="doctor_information" value="{{ json_encode($ship) }}">
                              <input type="hidden" name="data_cart" value="{{ json_encode($cart) }}">
                              <input type="hidden" name="data_cart_total" value="{{ json_encode($cart_total) }}">
                              <input type="hidden" name="data_grand_total" value="{{ json_encode($grand_total) }}">
                              <input type="hidden" name="discount" value="{{ json_encode($discount) }}">
                              <input type="hidden" name="tax" value="{{ json_encode($tax) }}">
                              <input type="hidden" name="billing" value="{{ json_encode($bill) }}">
                              <input type="hidden" name="shipping" value="{{ json_encode($shipping) }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                        <button class="btn btn-primary btn-sm" type="submit"><span>{{__('Submit')}}</span></button>
                    </div>
                </div>
            </div>
        </form>
        
        </div>

      </div>
      @include('includes.checkout_sitebar',$cart)

      {{-- <script type="text/javascript" src="{{asset('assets/front/js/payement.js')}}"></script> --}}

    </div>
  </div>
@endsection

{{-- @include('includes.checkout_modal') --}}