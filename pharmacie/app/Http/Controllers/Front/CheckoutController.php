<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Order,
    Models\PaymentSetting,
    Traits\StripeCheckout,
    Traits\MollieCheckout,
    Traits\PaypalCheckout,
    Traits\PaystackCheckout,
    Http\Controllers\Controller,
    Http\Requests\PaymentRequest,
    Traits\CashOnDeliveryCheckout,
    Traits\BankCheckout,
};
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Mail\AdminMail;
use App\Mail\ClientMail;
use App\Mail\LivraisonMail;
use App\Models\Currency;
use App\Models\Doctor_information;
use App\Models\Item;
use App\Models\Refill_order;
use App\Models\Setting;
use App\Models\ShippingService;
use App\Models\State;
use App\Models\User;
use App\Models\User_info_medical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mollie\Laravel\Facades\Mollie;

class CheckoutController extends Controller
{

    use StripeCheckout {
        StripeCheckout::__construct as private __stripeConstruct;
    }
    use PaypalCheckout {
        PaypalCheckout::__construct as private __paypalConstruct;
    }
    use MollieCheckout {
        MollieCheckout::__construct as private __MollieConstruct;
    }

    use BankCheckout;
    use PaystackCheckout;
    use CashOnDeliveryCheckout;

    public function __construct()
    {
        $setting = Setting::first();
        if ($setting->is_guest_checkout != 1) {
            $this->middleware('auth');
        }
        $this->middleware('localize');
        $this->__stripeConstruct();
        $this->__paypalConstruct();
    }

    public function ship_address()
    {
        if(Auth::user()==null){
            return redirect(route('user.login')) ;
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $data['user'] = Auth::user() ? Auth::user() : null;
        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {
            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item);
            }
        }

        $shipping = [];
        if (ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->exists()) {
            $shipping = ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->first();
            if ($cart_total >= $shipping->minimum_price) {
                $shipping = $shipping;
            } else {
                $shipping = [];
            }
        }

        if (!$shipping) {
            $shipping = ShippingService::whereStatus(1)->where('id', '!=', 1)->first();
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? Auth::user()->state->price : 0;
        $total_amount = $grand_total + $state_tax;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        return view('front.checkout.billing', $data);
    }


    public function billingStore(Request $request)
    {

        $user = User::find(Auth::user()->id);

         // Vérifiez si la case à cocher est cochée
         $refill_order = false;
        if ($request->has('refill_check')) {
            $refill_order = true;
            // dd($request->input('refill_check'));
        } 

        
        if ($request->same_ship_address) {
            Session::put('billing_address', $request->all());
            if (PriceHelper::CheckDigital()) {
                $shipping = [
                    "ship_first_name" => $request->bill_first_name,
                    "ship_last_name" => $request->delivery_name,
                    "ship_email" => $request->delivery_email,
                    "ship_phone" => $request->delivery_phone,
                    "ship_company" => $request->bill_company,
                    "ship_address1" => $request->delivery_adress,
                    "ship_address2" => $request->bill_address2,
                    "ship_zip" => $request->delivery_zip,
                    "ship_city" => $request->bill_city,
                    "ship_country" => $request->delivery_country,
                    "ship_patient_gender" => $request->sexe,
                    "ship_birthday" => $request->bill_date_birth,
                    "doctor_name" => $request->doctor_name,
                    "doctor_phone" => $request->doctor_phone,
                    "doctor_address" => $request->doctor_address,
                    "doctor_zip" => $request->doctor_zip,
                    "doctor_city" => $request->doctor_city,
                    "doctor_country" => $request->doctor_country,
                    "doctor_current_medical" => $request->doctor_current_medical,
                    "drug_allergie" => $request->drug_allergie,
                    "refill_order" => $refill_order
                    // "delivery_name" => $request->delivery_name,
                    // "delivery_email" => $request->delivery_email,
                    // "delivery_phone" => $request->delivery_phone,
                    // "delivery_adress" => $request->delivery_adress,

                ];
            } else {
                $shipping = [
                    "ship_first_name" => $request->bill_first_name,
                    "ship_last_name" => $request->bill_last_name,
                    "ship_email" => $request->bill_email,
                    "ship_phone" => $request->bill_phone,
                ];
            }
            Session::put('shipping_address', $shipping);
        } else {
            Session::put('billing_address', $request->all());
            Session::forget('shipping_address');
        }

        if (Session::has('shipping_address')) {
            // return redirect()->route('front.checkout.payment');
            return response()->json(['redirectTo' => route('front.checkout.payment')]);

        } else {
            // return redirect()->route('front.checkout.shipping');
            return response()->json(['redirectTo' => route('front.checkout.shipping')]);

        }
    }


    public function shipping()
    {

        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item);
            }
        }
        $shipping = [];
        if (ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->exists()) {
            $shipping = ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->first();
            if ($cart_total >= $shipping->minimum_price) {
                $shipping = $shipping;
            } else {
                $shipping = [];
            }
        }

        if (!$shipping) {
            $shipping = ShippingService::whereStatus(1)->where('id', '!=', 1)->first();
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        return view('front.checkout.shipping', $data);
    }

    public function shippingStore(Request $request)
    {
        Session::put('shipping_address', $request->all());
        return redirect(route('front.checkout.payment'));
    }


    public function sending_email_admin(Request $request)
    {
        $fichier_ordonnance =  $request->file('photo');

        $mime = $fichier_ordonnance->getMimeType();

        if (in_array($mime, ['image/jpeg', 'image/png'])) {
            // C'est une image
            // $path = $fichier_ordonnance->store('public/photos');
            $path = $fichier_ordonnance->storeAs('public/photos', $fichier_ordonnance->getClientOriginalName());

        } elseif ($mime == 'application/pdf') {
            // C'est un PDF
            // $path = $fichier_ordonnance->store('public/pdfs');
            $path = $fichier_ordonnance->storeAs('public/pdfs', $fichier_ordonnance->getClientOriginalName());

        } else {
            // Type de fichier non pris en charge
            return back()->withErrors(['photo_or_pdf' => 'Unsupported file type.']);
        }   

        $billing = $request->input('billing');
      
        $shipping = $request->input('shipping');
        
        $data_cart = $request->input('data_cart');
        $data_medicament = json_decode($data_cart, true);

        $data_count =  $request->input('discount');
        $data_count_all = json_decode($data_count, true);

        $data_all = $request->input('data_information');
        $data_client = json_decode($data_all, true);
        
        $data_doctor = $request->input('doctor_information');
        $data_doctor_all = json_decode($data_doctor, true);


        $data_client['data_cart'] =  $data_medicament;

        // dd($data_doctor_all);

        $shipping_info = [
            'ship_first_name' => $data_doctor_all['ship_first_name'],
            'ship_last_name' => $data_client['last_name'],
            'ship_email' => $data_doctor_all['ship_email'],
            'ship_phone' => $data_doctor_all['ship_phone'],
            'ship_adress' => $data_doctor_all['ship_address1'],
        ];
        $jsonShippingInfo = json_encode($shipping_info);
       
        $user = User::where('email',$data_client['email'])->first();
        
        $state = State::find(1);


        $orders = new Order([
            'user_id' => $user->id,
            'cart' => $data_cart,
            'shipping' =>  $shipping,
            'discount' =>  $data_count,
            'payment_method' => null,
            'txnid'  => null,
            'charge_id' => null,
            'transaction_number' => null,
            'order_status' => "Pending",
            'payment_status' => "unpaid",
            'shipping_info'=> $jsonShippingInfo,
            'billing_info' => $billing,
            'currency_sign' => "$",
            'currency_value' => 1,
            'tax' =>  1.3483,
        ]);
        $orders->save();

        $total_montant = PriceHelper::OrderTotal($orders);
        $valeurDecimale = floatval(str_replace(',', '', $total_montant));
        // dd($total_montant);
        
        $orders->update(['total_amount' => $valeurDecimale ]);
        $orders->save();

        $doctor_informations = new Doctor_information([
            'name' => $data_doctor_all['doctor_name'],
            'phone_number' => $data_doctor_all['doctor_address'],
            'adresse'=> $data_doctor_all['doctor_address'],
            'zip_code' => $data_doctor_all['doctor_zip'],
            'country'  => $data_doctor_all['doctor_country'],
            'order_id' => $orders->id,
        ]);
        $doctor_informations->save();

        // dd($doctor_informations->order_id);

        $user_info_medical = new User_info_medical([
            'gender' => $user->gender,
            'current_medical_condition' =>$data_doctor_all['doctor_current_medical'],
            'drug_allergie' =>$data_doctor_all['drug_allergie'],
            'photo_prescription' => $path,
            'order_id' => $orders->id,
        ]);
        $user_info_medical->save();

        $refill_order = new Refill_order([
            'value' => $data_doctor_all['refill_order'],
            'order_id' => $orders->id
        ]);
        $refill_order->save();

        $total_amount =PriceHelper::OrderTotal($orders);

        $data_client['data_doctor'] =  $data_doctor_all;
        $data_client['total_amount'] =  $total_amount;


        Mail::to('ambalafenofikambanana@gmail.com')->send(new AdminMail($data_client,$path,$data_medicament));
        Mail::to('manager@finestrxmeds.com')->send(new AdminMail($data_client,$path,$data_medicament));
        // Mail::to("harilovajohnny@gmail.com")->send(new ClientMail($data_client));
        Mail::to($user->email)->send(new ClientMail($data_client));
        return redirect()->route('front.checkout.livraison');
    }

    public function livraison_page(Request $request){
        Session::flash('success', 'Email envoyé avec succès!');
        return view('front.checkout.livraison');
    }

    public function payment()
    {

        if (!Session::has('billing_address')) {
            return redirect(route('front.checkout.billing'));
        }

        if (!Session::has('shipping_address')) {
            return redirect(route('front.checkout.shipping'));
        }


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item);
            }
        }
        $shipping = [];
        if (ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->exists()) {
            $shipping = ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->first();
            if ($cart_total >= $shipping->minimum_price) {
                $shipping = $shipping;
            } else {
                $shipping = [];
            }
        }

        if (!$shipping) {
            $shipping = ShippingService::whereStatus(1)->where('id', '!=', 1)->first();
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
  
        return view('front.checkout.payment', $data);
    }




    public function checkout(PaymentRequest $request)
    {

        Session::forget('cart');
        Session::forget('discount');
        Session::forget('coupon');

        $id_user =Auth::user()->id;
        $order_id = Order::where('user_id',$id_user)->max('id');
        // $order_id = Session::get('order_id');
        $order = Order::find($order_id);
        $cart = json_decode($order->cart, true);
        $setting = Setting::first();
        if ($setting->is_twilio == 1) {
            // message
            $sms = new SmsHelper();
            $user_number = $order->user->phone;
            if ($user_number) {
                $sms->SendSms($user_number, "'purchase'");
            }
        }
        return view('front.checkout.success', compact('order', 'cart'));

        // $input = $request->all();
        // $checkout = false;
        // $payment_redirect = false;
        // $payment = null;

        // if (Session::has('currency')) {
        //     $currency = Currency::findOrFail(Session::get('currency'));
        // } else {
        //     $currency = Currency::where('is_default', 1)->first();
        // }


        // $usd_supported = array(
        //     "USD", "AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG",
        //     "AZN", "BAM", "BBD", "BDT", "BGN", "BIF", "BMD", "BND", "BOB", "BRL",
        //     "BSD", "BWP", "BYN", "BZD", "CAD", "CDF", "CHF", "CLP", "CNY", "COP",
        //     "CRC", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EGP", "ETB", "EUR",
        //     "FJD", "FKP", "GBP", "GEL", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD",
        //     "HNL", "HTG", "HUF", "IDR", "ILS", "INR", "ISK", "JMD", "JPY", "KES",
        //     "KGS", "KHR", "KMF", "KRW", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD",
        //     "LSL", "MAD", "MDL", "MGA", "MKD", "MMK", "MNT", "MOP", "MUR", "MVR",
        //     "MWK", "MXN", "MYR", "MZN", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD",
        //     "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RON", "RSD",
        //     "RUB", "RWF", "SAR", "SBD", "SCR", "SEK", "SGD", "SHP", "SLE", "SOS",
        //     "SRD", "STD", "SZL", "THB", "TJS", "TOP", "TRY", "TTD", "TWD", "TZS",
        //     "UAH", "UGX", "UYU", "UZS", "VND", "VUV", "WST", "XAF", "XCD", "XOF",
        //     "XPF", "YER", "ZAR", "ZMW"
        // );

        // $paypal_supported = ['USD', 'EUR', 'AUD', 'BRL', 'CAD', 'HKD', 'JPY', 'MXN', 'NZD', 'PHP', 'GBP', 'RUB'];
        // $paystack_supported = ['NGN', "GHS"];
        // switch ($input['payment_method']) {

        //     case 'Stripe':
        //         if (!in_array($currency->name, $usd_supported)) {
        //             Session::flash('error', __('Currency Not Supported'));
        //             return redirect()->back();
        //         }
        //         $checkout = true;
        //         $payment_redirect = true;
        //         $payment = $this->stripeSubmit($input);
        //         break;

        //     case 'Paypal':
        //         if (!in_array($currency->name, $paypal_supported)) {
        //             Session::flash('error', __('Currency Not Supported'));
        //             return redirect()->back();
        //         }
        //         $checkout = true;
        //         $payment_redirect = true;
        //         $payment = $this->paypalSubmit($input);
        //         break;


        //     case 'Mollie':
        //         if (!in_array($currency->name, $usd_supported)) {
        //             Session::flash('error', __('Currency Not Supported'));
        //             return redirect()->back();
        //         }
        //         $checkout = true;
        //         $payment_redirect = true;
        //         $payment = $this->MollieSubmit($input);
        //         break;

        //     case 'Paystack':
        //         if (!in_array($currency->name, $paystack_supported)) {
        //             Session::flash('error', __('Currency Not Supported'));
        //             return redirect()->back();
        //         }
        //         $checkout = true;
        //         $payment = $this->PaystackSubmit($input);

        //         break;

        //     case 'Bank':
        //         $checkout = true;
        //         $payment = $this->BankSubmit($input);
        //         break;

        //     case 'Cash On Delivery':
        //         $checkout = true;
        //         $payment = $this->cashOnDeliverySubmit($input);
        //         break;
        // }



        // if ($checkout) {
        //     if ($payment_redirect) {
        //         if ($payment['status']) {
        //             return redirect()->away($payment['link']);
        //         } else {
        //             Session::put('message', $payment['message']);
        //             return redirect()->route('front.checkout.cancle');
        //         }
        //     } else {
        //         if ($payment['status']) {
        //             return redirect()->route('front.checkout.success');
        //         } else {
        //             Session::put('message', $payment['message']);
        //             return redirect()->route('front.checkout.cancle');
        //         }
        //     }
        // } else {
        //     return redirect()->route('front.checkout.cancle');
        // }
        
    }

    public function paymentRedirect(Request $request)
    {
        $responseData = $request->all();

        if (isset($responseData['session_id'])) {
            $payment = $this->stripeNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } elseif (Session::has('order_payment_id')) {
            $payment = $this->paypalNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function mollieRedirect(Request $request)
    {

        $responseData = $request->all();

        $payment = Mollie::api()->payments()->get(Session::get('payment_id'));
        $responseData['payment_id'] = $payment->id;
        if ($payment->status == 'paid') {
            $payment = $this->mollieNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentSuccess()
    {
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            $order = Order::find($order_id);
            $cart = json_decode($order->cart, true);
            $setting = Setting::first();
            if ($setting->is_twilio == 1) {
                // message
                $sms = new SmsHelper();
                $user_number = $order->user->phone;
                if ($user_number) {
                    $sms->SendSms($user_number, "'purchase'");
                }
            }
            // dd($cart);
            return view('front.checkout.success', compact('order', 'cart'));
        }
        return redirect()->route('front.index');
    }



    public function paymentCancle()
    {
        $message = '';
        if (Session::has('message')) {
            $message = Session::get('message');
            Session::forget('message');
        } else {
            $message = __('Payment Failed!');
        }
        Session::flash('error', $message);
        return redirect()->route('front.checkout.billing');
    }

    public function stateSetUp($state_id)
    {

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            if ($item->tax) {
                $total_tax += $item::taxCalculate($item);
            }
        }

        $shipping = [];
        if (ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->exists()) {
            $shipping = ShippingService::whereStatus(1)->whereId(1)->whereIsCondition(1)->first();
            if ($cart_total >= $shipping->minimum_price) {
                $shipping = $shipping;
            } else {
                $shipping = [];
            }
        }

        if (!$shipping) {
            $shipping = ShippingService::whereStatus(1)->where('id', '!=', 1)->first();
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id) {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }
}
