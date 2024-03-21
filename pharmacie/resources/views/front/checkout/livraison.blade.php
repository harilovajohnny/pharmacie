@extends('master.front')
@section('title')
    {{__('Shipping')}}
@endsection
@section('content')
    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="column">
        <ul class="breadcrumbs">
          <li><a href="{{route('front.index')}}">{{ __('Home') }}</a> </li>
          <li class="separator"></li>
          <li>{{ __('') }}</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1  checkut-page">

    @if(session('success'))
        <div class="alert alert-success col-xl-9 " id="successMessage">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
      <!-- Payment Methode-->
      <div class="col-xl-9 col-lg-8">
        <div class="steps flex-sm-nowrap mb-5">
          <a class="step " href="{{ route('front.checkout.billing') }}">
            <h4 class="step-title"><i class="icon-check-circle"></i>1. {{ __('Billing Address') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title"><i class="icon-check-circle"></i>2. {{ __('Delivery Adress') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title"><i class="icon-check-circle"></i>3. {{ __('Doctor Information') }}:</h4>
          </a>
          <a class="step" href="">
              <h4 class="step-title"><i class="icon-check-circle"></i>4. {{ __('Review and pay') }}</h4>
          </a>
          <a class="step active" href="">
              <h4 class="step-title">5. {{ __('Shipping ') }}</h4>
          </a>
         
        </div>
        <div class="card">
            <div class="card-body">
              <h6 class="pb-2">{{__('Review Your Order')}} :</h6>
              <hr>
              <div class="row" style="padding-top: 50px;padding-bottom: 10%;">
                
                <div class="card col-md-5"  style="margin-left: 52px;">
                  <div class="card-header" style="background-color: #4f8ecd;color:white;padding-top: 30px;padding-bottom: 30px;">
                    <h3 style="font-size: 21px;font-weight: 700;line-height: 24px;color: var(--dark);text-align: center;">FREE SHIPPING</h3>
                  </div>
                  <div class="card-body" onclick="selectCard(this)"  style="text-align: center;padding-bottom: 30px;border: 1px solid #d8dadb;">
                    <h2 style="font-weight: 400;">0 $</h2>
                    <span style="font-weight: 600"><i class="icon-check"></i> Delivery Time: 25 to 30 days</span>
                    <div style="padding-top:10px">
                      <button class="btn btn-primary btn-block mt-2 mx-0" type="submit" data-bs-toggle="modal" data-bs-target="#first-livraison" style="background:#4f8ecd!important">submit</button>
                    </div>
                  </div>
                </div>

                <div class="card col-md-5" style="margin-left: 30px;">
                  <div class="card-header" style="background-color: #3fc386;color:white;padding-top: 30px;padding-bottom: 30px;">
                    <h3 style="font-size: 21px;font-weight: 700;line-height: 24px;color: var(--dark);text-align: center;">EXPRESS SHIPPING</h3>
                  </div>
                  <div class="card-body" onclick="selectCard(this)"  style="text-align: center;padding-bottom: 30px;border: 1px solid #d8dadb;">
                    <h2 style="font-weight: 400;">25 $</h2>
                    <span style="font-weight: 600"><i class="icon-check"></i>  Delivery Time: 7 to 10 days</span>
                    <div style="padding-top:10px">
                      <button class="btn btn-primary btn-block mt-2 mx-0" type="submit" data-bs-toggle="modal" data-bs-target="#second-livraison" style="background:#3fc386!important">submit</button>
                    </div>
                  </div>
                </div>

                {{-- <div class="card col-md-5" onclick="selectCard(this)">
                  <div class="card-header" style="background-color: #3fc386;color:white"> EXPRESS SHIPPING <span style="float: right;"> 27$</span></div>
                  <div class="card-body" style="border:1px solid #ebebeb" >
                    <p>Delivery Time: 7 to 10 days</p>
                  </div>
                </div>   --}}
              </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="first-livraison" tabindex="-1"  aria-hidden="true">
      <div class="modal-dialog" >
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">{{__('Free Shipping')}}</h6>
            <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <form action="{{route('front.checkout.submit')}}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" value="Cash On Delivery" id="">
            <input type="hidden" name="state_id" value="{{auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : ''}}" class="state_id_setup">
            <div class="card-body">
              <p>Delivery with zero fees after 25 to 30 days. </p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
              <button class="btn btn-primary btn-sm" type="submit"><span>{{__('Send')}}</span></button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="second-livraison" tabindex="-1"  aria-hidden="true">
      <div class="modal-dialog" >
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">{{__('Express Shipping')}}</h6>
            <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <form action="{{route('front.checkout.submit')}}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" value="Cash On Delivery" id="">
            <input type="hidden" name="state_id" value="{{auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : ''}}" class="state_id_setup">
            <div class="card-body">
              <p>Delivery after 7 to 10 days. </p>
            </div>
          <div class="modal-footer">
            <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
            <button class="btn btn-primary btn-sm" type="submit"><span>{{__('Send')}}</span></button>
          </form>
          </div>
        </div>
      </div>
    </div>

      {{-- <script type="text/javascript" src="{{asset('assets/front/js/payement.js')}}"></script> --}}

    </div>
  </div>

  {{-- <script>
    
  </script> --}}

@endsection

      {{-- @include('includes.checkout_sitebar',$cart) --}}