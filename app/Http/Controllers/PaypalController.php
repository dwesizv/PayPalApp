<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal;

class PaypalController extends Controller {

    function about() {
    }

    function add(Request $request, $id) {
        if($id == 1 || $id == 2) {
            $cart = session()->get('cart', []);
            if(isset($cart[$id])) {
                $cart[$id]['quantity']++;
            } else {
                $cart[$id] = [
                    'name' => 'Producto ' . $id,
                    'quantity' => 1,
                    'price' => $id * 3.5,
                ];
            }
            session()->put('cart', $cart);
        }
        return back();
    }

    private function cartTotal() {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        return $total;
    }

    function checkout() {
        $total = $this->cartTotal();
        if($total <= 0) {
            return redirect()->route('main')->withErrors(['message' => 'No hay productos en el carrito para tramitar el pedido.']);
        }
        return view('checkout', [
            'cart' => session()->get('cart', []),
            'total' => $this->cartTotal()
        ]);
    }

    function home() {
    }

    function login() {
    }

    function logout() {
    }

    function main() {
        return view('main', [
            'cart' => session()->get('cart', []),
            'total' => $this->cartTotal()
        ]);
    }

    function pay(Request $request) {
        $total = $this->cartTotal();
        if($total > 0) {
            session()->put('name', $request->input('name'));
            session()->put('email', $request->input('email'));
            session()->put('address', $request->input('address'));
            return view('pay', ['total' => $total]);
        } else {
            return redirect()->route('main')->withErrors(['message' => 'No hay productos en el carrito para pagar.']);
        }
    }

    function paypalApprove(Request $request) {
        $data = $request->all();
        $orderId = $data['orderId'];
        $order = Order::where('paypal_order_id', $orderId)->first();
        if($order == null) {
            return response()->json(['error' => 'No se ha creado el pago que se está validando'], 500);
        }
        $provider = new PayPal();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $data = $provider->capturePaymentOrder($orderId);
        if(isset($data['purchase_units'][0]['payments']['captures'][0]['amount']['value']) &&
                isset($data['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code']) &&
                isset($data['status'])) {
            $paypalAmount = $data['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $paypalCurrency = $data['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $paypalStatus = $data['status'];
            if($paypalAmount == $order->amount &&
                    $paypalCurrency == $order->currency &&
                    $paypalStatus == 'COMPLETED') {
                $order->capture_id = $data['purchase_units'][0]['payments']['captures'][0]['id'];
                $order->status = 'completed';
                $order->save();
                return response()->json(['status' => $data['status']]);
            } else {
                $order->capture_id = $data['purchase_units'][0]['payments']['captures'][0]['id'];
            }
        }
        $order->status = 'failed';
        $order->save();
        return response()->json(['status' => 'ERROR'], 500);
    }   

    function paypalApproved() {
        session()->forget('cart');
        session()->forget('name');
        session()->forget('email');
        session()->forget('address');
        return view('paypal.approved');
    }

    function paypalCancel(Request $request) {
        $data = $request->all();
        $orderId = $data['orderId'];
        $order = Order::where('paypal_order_id', $orderId)->first();
        if ($order) {
            $order->status = 'canceled';
            $order->save();
        }
        return response()->json(['status' => 'canceled']);
    }

    function paypalCanceled() {
        return view('paypal.canceled');
    }

    private function paypalCreateOrder($orderId, $value) {
        Order::create([
            'address' => session()->get('address'),
            'amount' => $value,
            'cart' => json_encode(session()->get('cart')),
            'currency' => env('PAYPAL_CURRENCY'),
            'email' => session()->get('email'),
            'name' => session()->get('name'),
            'paypal_order_id' => $orderId,
            'status' => 'CREATED'
        ]);
    }

    function paypalError() {
        return view('paypal.error');
    }

    function paypalNotApproved() {
        return view('paypal.notapproved');
    }

    function paypalPay(Request $request) {
        $value = number_format($this->cartTotal(), 2, '.', '');
        if($value <= 0) {
            return response()->json(['error' => 'No se puede crear un pago'], 500);
        }
        $provider = new PayPal();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $order = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => env('PAYPAL_CURRENCY'),
                        'value' => $value
                    ]
                ]
            ]
        ]);
        if (!isset($order['id'])) {
            return response()->json(['error' => 'Error al crear el pago'], 500);
        }
        $this->paypalCreateOrder($order['id'], $value);
        return response()->json(['orderId' => $order['id']]);
    }

    function substract(Request $request, $id) {
        if($id == 1 || $id == 2) {
            $cart = session()->get('cart', []);
            if(isset($cart[$id])) {
                $cart[$id]['quantity']--;
                if($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
            }
            session()->put('cart', $cart);
        }
        return back();
    }

    function verifyWebhook(Request $request) {
        $provider = new PayPal();
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);
        $provider->setWebHookID(env('PAYPAL_WEBHOOK'));
        $result = $provider->verifyIPN($request);
        return $result['verification_status'] === 'SUCCESS';
    }

    function webhook(Request $request) {
        $payload = $request->all();
        if ($payload['event_type'] === 'PAYMENT.CAPTURE.COMPLETED') {
            $orderId = $payload['resource']['supplementary_data']['related_ids']['order_id'];
            $order = Order::where('paypal_order_id', $orderId)->first();
            if ($order) {
                $result = $this->verifyWebhook($request);
                if($result) {
                    $order->webhook = 'success';
                } else {
                    $order->webhook = 'failure';
                }
                $order->save();
            }
        }
        return response()->json([]);
    }
}