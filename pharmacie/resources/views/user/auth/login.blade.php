@extends('master.front')
@section('title')
    {{__('Login')}}
@endsection
@section('content')

    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                <li class="separator"></li>
                <li>{{__('Login/Register')}}</li>
              </ul>
          </div>
      </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1">
    <div class="row">
      <div class="col-md-5">
        <form class="card" method="post" action="{{route('user.login.submit')}}">
            @csrf
          <div class="card-body ">
            <h4 class="margin-bottom-1x text-center">{{__('Login')}}</h4>

            <div class="form-group input-group">
              <input class="form-control" type="email" name="login_email" placeholder="{{ __('Email') }}" value="{{old('login_email')}}"><span class="input-group-addon"><i class="icon-mail"></i></span>
            </div>
            @error('login_email')
              <p class="text-danger">{{$message}}</p>
              @enderror

            <div class="form-group input-group">
              <input class="form-control" type="password" name="login_password" placeholder="{{ __('Password') }}" ><span class="input-group-addon"><i class="icon-lock"></i></span>
            </div>
            @error('login_password')
                <p class="text-danger">{{$message}}</p>
            @enderror

            <div class="d-flex flex-wrap justify-content-between padding-bottom-1x">
              <div class="custom-control custom-checkbox">
                <input class="custom-control-input" type="checkbox" id="remember_me">
                <label class="custom-control-label" for="remember_me">{{__('Remember me')}}</label>
              </div><a class="navi-link" href="{{route('user.forgot')}}">{{__('Forgot password?')}}</a>
            </div>
           
              <button class="btn btn-primary btn-block btn-lg mt-2" type="submit"><span>{{ __('Login') }}</span></button>
              {{-- <div class="text-center">Or </div> 
              <a class="btn btn-primary btn-block btn-lg mt-2" href=""  style="background: #28a745!important"><span>{{ __('Register') }}</span></a href=""> --}}
            <div class="row">
                <div class="col-lg-12 text-center mt-3">
                @if($setting->facebook_check == 1)
                <a class="facebook-btn mr-2" href="{{route('social.provider','facebook')}}">{{ __('Facebook login') }}
                </a>
                @endif
                @if($setting->google_check == 1)
                <a class="google-btn" href="{{route('social.provider','google')}}"> {{ __('Google login') }}
                </a>
                @endif
              </div>
              </div>
          </div>
        </form>
      </div>
      <div class="col-md-7">
        <div class="card register-area">
            <div class="card-body ">
                <h4 class="margin-bottom-1x text-center">{{__('Register')}}</h4>
        <form class="row" action="{{route('user.register.submit')}}" method="POST">
            @csrf
          <div class="col-sm-6">
            <div class="form-group">
              <label for="reg-fn">{{__('First Name')}}</label>
              <input class="form-control" type="text" name="first_name" placeholder="{{__('First Name')}}" id="reg-fn" value="{{old('first_name')}}" required>
            @error('first_name')
            <p class="text-danger">{{$message}}</p>
            @endif
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="reg-ln">{{__('Last Name')}}</label>
              <input class="form-control" type="text" name="last_name" placeholder="{{__('Last Name')}}" id="reg-ln" value="{{old('last_name')}}" required>
              @error('last_name')
            <p class="text-danger">{{$message}}</p>
            @endif
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              <label>Sexe</label>
              <div class="row" style="padding-left: 26px"> 
                <div class="col-sm-5">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="exampleRadios1" value="Male" checked>
                    <label class="form-check-label" for="exampleRadios1">
                      Male
                    </label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="exampleRadios2" value="Female">
                    <label class="form-check-label" for="exampleRadios2">
                      Female
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="checkout-fn">{{__('Date birth')}}</label>
              <input class="form-control" type="date" name="date_birth" id="checkout-fn" required>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="reg-email">{{__('E-mail Address')}}</label>
              <input class="form-control" type="email" name="email" placeholder="{{__('E-mail Address')}}" id="reg-email" value="{{old('email')}}" required>
              @error('email')
              <p class="text-danger">{{$message}}</p>
              @endif
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="reg-phone">{{__('Phone Number')}}</label>
              <input class="form-control" name="phone" type="text" placeholder="{{__('Phone Number')}}" id="reg-phone" value="{{old('phone')}}" required>
              @error('phone')
              <p class="text-danger">{{$message}}</p>
              @endif
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="checkout-zip">{{__('Zip Code')}}</label>
              <input class="form-control" name="ship_zip" type="text" placeholder="Zip code" required>
              @error('ship_zip')
                <p class="text-danger">{{$message}}</p>
              @endif
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="checkout-city">{{__('City')}}</label>
              <input class="form-control" name="city" type="text" id="checkout-city" placeholder="City" required>
              @error('city')
                <p class="text-danger">{{$message}}</p>
              @endif
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="checkout-address1">{{__('Address')}} 1</label>
              <input class="form-control" name="adresse" placeholder="adresse"  type="text" required>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
              <label for="checkout-country">{{ __('Country') }}</label>
              <select class="form-control" required name="bill_country" id="billing-country">
                <option selected>{{__('Choose Country')}}</option>
                @foreach (DB::table('countries')->get() as $country)
                      <option value="{{$country->name}}" {{isset($user) && $user->bill_country == $country->name ? 'selected' :''}} >{{$country->name}}</option>
                  @endforeach
              </select>
            </div>
          </div>


          <div class="col-sm-6">
            <div class="form-group">
              <label for="reg-pass">{{__('Password')}}</label>
              <input class="form-control" type="password" name="password" placeholder="{{__('Password')}}" id="reg-pass">
              @error('password')
              <p class="text-danger">{{$message}}</p>
              @endif
            </div>

          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label for="reg-pass-confirm">{{__('Confirm Password')}}</label>
              <input class="form-control" type="password" name="password_confirmation" placeholder="{{__('Confirm Password')}}" id="reg-pass-confirm">
            </div>
          </div>

          @if ($setting->recaptcha == 1)
          <div class="col-lg-12 mb-4">
              {!! NoCaptcha::renderJs() !!}
              {!! NoCaptcha::display() !!}
              @if ($errors->has('g-recaptcha-response'))
              @php
                  $errmsg = $errors->first('g-recaptcha-response');
              @endphp
              <p class="text-danger mb-0">{{__("$errmsg")}}</p>
              @endif
          </div>
          @endif

          <div class="col-12 text-center">
            <button class="btn btn-primary margin-bottom-none" type="submit"><span>{{__('Register')}}</span></button>
          </div>
        </form>
            </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Site Footer-->
@endsection
