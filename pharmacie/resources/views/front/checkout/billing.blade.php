@extends('master.front')

@section('title')
    {{__('Billing')}}
@endsection

@section('content')
    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="column">
        <ul class="breadcrumbs">
          <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
          <li class="separator"></li>
          <li>{{__('Billing address')}}</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1 checkut-page">
    <div class="row">
      <!-- Billing Adress-->
      <div class="col-xl-9 col-lg-8">
        <div class="steps flex-sm-nowrap mb-5">
          <a class="step active" href="{{ route('front.checkout.billing') }}">
              <h4 class="step-title">1. {{ __('Billing Address') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title">2. {{ __('Delivery Adress') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title">3. {{ __('Doctor Information') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title">4. {{ __('Review and pay') }}</h4>
          </a>
          <a class="step" href="">
            <h4 class="step-title">5. {{ __('Shipping') }}</h4>
          </a>
        </div>

      <form id="checkoutBilling" action="{{route('front.checkout.store')}}" method="POST">

        <div id="loader" style="display: none">
          <div id="preloader">
            <img src="{{ asset('assets/images/'.$setting->loader) }}" alt="{{ __('Loading...') }}">
        </div>
        </div>
        <div class="card" id="card_billing">
          <div class="card-body">
            {{-- <h6>{{__('Billing Address')}}</h6> --}}
              @csrf

            <div class="row">
              
              <div class="col-sm-6">
                <div class="form-group">
                  <h6>{{__('Billing Address')}}</h6>
                  <ul class="list-unstyled">
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Name')}}: </span>{{ $user->first_name }}  {{ $user->last_name }}</li>
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Gender')}}: </span>{{ $user->gender }} </li>
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Birthday')}}: </span>{{ $user->date_birth }} </li>
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Email')}}: </span>{{ $user->email }}</li>
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Address')}}: </span>{{ $user->ship_address1 }}</li>
                  
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Phone')}}: </span>{{ $user->phone }}</li>
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('City')}}: </span>{{ $user->ship_city }}</li>
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Country')}}: </span>{{ $user->bill_country }}</li>
                  </ul>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <h6>{{__('Shipping Address')}}</h6>
                  <ul class="list-unstyled">
                    <li class="patient-information detail-bulling"><span class="detail-color">{{__('Name')}}: </span>{{ $user->first_name }}  {{ $user->last_name }}</li>
                       <li class="patient-information detail-bulling"><span class="detail-color">{{__('Email')}}: </span>{{ $user->email }}</li>
                      <li class="patient-information detail-bulling"><span class="detail-color">{{__('Address')}}: </span>{{ $user->ship_address1 }}</li>
                    </ul>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between paddin-top-1x mt-4">
              <a class="btn btn-primary btn-sm" href="{{route('front.cart')}}"><span class="hidden-xs-down"><i class="icon-arrow-left"></i>{{__('Back To Cart')}}</span></a>
              <button class="btn btn-primary btn-sm" id="showSecondCard" type="submit"><span class="hidden-xs-down">{{__('Continue')}}</span><i class="icon-arrow-right"></i></button>
            </div>

          </div>
        </div>

        <div class="card" id="card_third" style="margin-top: 36px; display: none;">
          <div class="card-body">
              <div class="form-group">
                <h6>{{__('Shipping Address')}}</h6>
                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-2 delivery-text">
                      <span class="detail-color">{{__('Name')}} </span>
                    </div>
                    <div class="col-sm-6">
                        <input class="form-control" name="delivery_name" placeholder="adresse" required type="text" value="{{ $user->first_name  }} {{ $user->last_name  }}">
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-2 delivery-text">
                      <span class="detail-color">{{__('Email')}} </span>
                    </div>
                    <div class="col-sm-6">
                        <input class="form-control" name="delivery_email" placeholder="adresse" required type="text" id="checkout-address1" value="{{isset($user) ? $user->email : ''}}">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-2 delivery-text">
                      <span class="detail-color">{{__('Phone')}} </span>
                    </div>
                    <div class="col-sm-6 ">
                        <input class="form-control" name="delivery_phone" placeholder="adresse" required type="text" value="{{isset($user) ? $user->phone : ''}}">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-2 delivery-text">
                      <span class="detail-color">{{__('Address')}} </span>
                    </div>
                    <div class="col-sm-6">
                        <input class="form-control" name="delivery_adress" placeholder="adresse" required type="text" id="checkout-address1" value="{{isset($user) ? $user->ship_address1 : ''}}">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-2 delivery-text">
                      <span class="detail-color">{{__('Zip code')}} </span>
                    </div>
                    <div class="col-sm-6 ">
                      <input class="form-control" name="delivery_zip" type="text" id="checkout-zip" placeholder="Zip code" value="{{isset($user) ? $user->ship_zip : ''}}">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-sm-2 delivery-text">
                      <span class="detail-color">{{__('Country')}} </span>
                    </div>
                    <div class="col-sm-6 ">
                      <select class="form-control" required name="delivery_country" id="billing-country">
                        <option selected>{{__('Choose Country')}}</option>
                        @foreach (DB::table('countries')->get() as $country)
                              <option value="{{$country->name}}" {{isset($user) && $user->bill_country == $country->name ? 'selected' :''}} >{{$country->name}}</option>
                          @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input  type="checkbox" id="refill_check" name="refill_check" value="true">
                    <label class="custom-control-label" for="refill_check">Click to Accept Refill</label>
                  </div>
                </div>

              </div>

              <div class="d-flex justify-content-between paddin-top-1x mt-4">
                <button class="btn btn-primary btn-sm" id="backCardbilling" ><span class="hidden-xs-down"><i class="icon-arrow-left"></i>{{__('Billing Adress')}}</span></button>
                <button class="btn btn-primary btn-sm" id="showthirdCard" type="submit"><span class="hidden-xs-down">{{__('Continue')}}</span><i class="icon-arrow-right"></i></button>
              </div>
          </div>
        </div>

        <div class="card" id="card_doctor"  style="margin-top: 36px;display:none">
          <div class="card-body">
            <h6>{{__('Doctor Information')}}</h6>

            {{-- <form id="checkoutBilling" action="{{route('front.checkout.store')}}" method="POST"> --}}
              @csrf
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="checkout-fn">{{__('Doctor Name')}}</label>
                    {{-- <span class="detail-color">{{__('Doctor Name')}} </span> --}}
                    <input class="form-control" name="doctor_name" type="text" placeholder="Dr name" required  >
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="checkout-phone">{{__('Phone Number')}}</label>
                    <input class="form-control" name="doctor_phone" type="text" id="checkout-phone" placeholder="phone number" required >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="checkout-address1">{{__('Address')}} </label>
                    <input class="form-control" name="doctor_address" required type="text" placeholder="Adress"  id="checkout-address1"  >
                  </div>
                </div>  
              </div>

              @if (PriceHelper::CheckDigital())
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="checkout-zip">{{__('Zip Code')}}</label>
                    <input class="form-control" name="doctor_zip" type="text" placeholder="Zip code"  id="checkout-zip" >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="checkout-city">{{__('City')}}</label>
                    <input class="form-control" name="doctor_city" type="text" placeholder="city" required id="checkout-city" >
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="checkout-country">{{ __('Country') }}</label>
                    <select class="form-control" required name="doctor_country" id="billing-country">
                      <option selected>{{__('Choose Country')}}</option>
                      @foreach (DB::table('countries')->get() as $country)
                            <option value="{{$country->name}}" {{isset($user) && $user->bill_country == $country->name ? 'selected' :''}} >{{$country->name}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
              </div>
              @endif


              <div class="row">

                <input name="bill_first_name" type="hidden"  id="checkout-fn" value="{{isset($user) ? $user->first_name : ''}}">
                <input name="bill_last_name" type="hidden"  id="checkout-ln" value="{{isset($user) ? $user->last_name : ''}}">
                <input name="bill_date_birth" type="hidden"  id="checkout-fn" value="{{isset($user) ? $user->date_birth : ''}}"">
                <input name="bill_email"  type="hidden" id="checkout_email_billing" value="{{isset($user) ? $user->email : ''}}">
                <input name="bill_phone" type="hidden"  id="checkout-phone" value="{{isset($user) ? $user->phone : ''}}">
                <input name="bill_address1"  type="hidden" id="checkout-address1" value="{{isset($user) ? $user->ship_address1 : ''}}">
                <input name="bill_zip" type="hidden" id="checkout-zip"  value="{{isset($user) ? $user->ship_zip : ''}}">
                <input name="bill_city" type="hidden" id="checkout-city"  value="{{isset($user) ? $user->ship_city : ''}}">
                <input name="bill_country" type="hidden" id="billing-country"  value="{{isset($user) ? $user->bill_country : ''}}">

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="current_medical">{{__('Current Medical conditions')}}</label>
                    <input class="form-control" name="doctor_current_medical" type="text" id="current_medical" placeholder="Current Medical conditions" required >
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="drug_allergie">{{__('Know Drug Allergies')}} </label>
                    <input class="form-control" name="drug_allergie" required type="text" placeholder="Know Drug Allergies"  id="drug_allergie"  >
                  </div>
                </div>  
              </div>

              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input class="custom-control-input" type="checkbox" id="same_address" name="same_ship_address" {{Session::has('shipping_address') ? 'checked' : ''}} >
                  <label class="custom-control-label" for="same_address">{{__('Same as billing address')}}</label>
                </div>
              </div>

              @if ($setting->is_privacy_trams == 1)
              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input class="custom-control-input" type="checkbox" id="trams__condition" >
                  <label class="custom-control-label" for="trams__condition">This site is protected by reCAPTCHA and the <a href="{{$setting->policy_link}}" target="_blank">Privacy Policy</a> and <a href="{{$setting->terms_link}}" target="_blank">Terms of Service</a> apply.</label>
                </div>
              </div>
              @endif

              <div class="d-flex justify-content-between paddin-top-1x mt-4">
                {{-- <a class="btn btn-primary btn-sm" href="{{route('front.cart')}}"><span class="hidden-xs-down"><i class="icon-arrow-left"></i>{{__('Billing adress')}}</span></a> --}}
                <button class="btn btn-primary btn-sm" id="backToFirstCard" ><span class="hidden-xs-down"><i class="icon-arrow-left"></i>{{__('Delivery Adress')}}</span></button>
                @if ($setting->is_privacy_trams == 1)
                <button disabled id="continue__button" class="btn btn-primary  btn-sm" type="button"><span class="hidden-xs-down">{{__('Continue')}}</span><i class="icon-arrow-right"></i></button>
                @else
                <button class="btn btn-primary btn-sm" type="submit"><span class="hidden-xs-down">{{__('Continue')}}</span><i class="icon-arrow-right"></i></button>
                @endif
              </div>
            {{-- </form> --}}
          </div>
        </div>
      </form>

      </div>
      <!-- Sidebar          -->
      @include('includes.checkout_sitebar',$cart)


    </div>
  </div>
@endsection
