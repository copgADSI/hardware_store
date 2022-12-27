<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dashboards\CartDashboard;
use App\Models\Address;
use App\Models\OrderState;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentSate;
use App\Models\ProductOrder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    protected const CURRENCY = 'COP';
    protected $products;
    protected $stripe;
    protected $session;
    protected $cartDashboard;
    public function __construct(CartDashboard $cartDashboard)
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $this->cartDashboard = $cartDashboard;
    }
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function poorPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'number' => 'min:15|numeric',
                'cvc' => 'required|numeric',
                'expiration_month' => 'required',
                'expiration_year' => 'required',
                'name_on_card' => 'required',
                'phone' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            $card_response = $this->fillCard($request->all());
            $this->products = $this->cartDashboard->getCartData($request->user_id);
            if (is_null($this->products)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tu carrito está vacío',
                ], 404);
            }
            $this->createCheckoutSession($request->user_id);
            $response_data = $this->stripe->charges->create([
                'amount' => $this->products->sum('subtotal'),
                'currency' => self::CURRENCY,
                'source' => $card_response->id,
                'description' => 'My First Test Charge (created for API docs at https://www.stripe.com/docs/api)',
            ]);
            return redirect(route('payment.success', ['session_id' => $this->session->id]));
        } catch (Exception $ex) {
            return response()->json(['response' => $ex->getMessage()], 500);
        }
    }

    /**
     * método encargado de llenar la tarjeta
     * @param array $card
     */
    private function fillCard(array $card)
    {
        return $this->stripe->tokens->create([
            'card' => [
                'number' => $card['number'],
                'exp_month' => $card['expiration_month'],
                'exp_year' => $card['expiration_year'],
                'cvc' => $card['cvc'],
            ],
        ]);
    }

    /**
     * método usado para crear una sesión de comprobación de pago
     * @param int $user_id
     */
    private function createCheckoutSession(int $user_id)
    {
        $line_items = [];
        $amount = 0;
        foreach ($this->products as $product) {
            $amount += $product->subtotal;
            $line_items[] = [
                'price_data' => [
                    'currency' => self::CURRENCY,
                    'product_data' => ['name' => $product->name],
                    'unit_amount' => $product->price,
                ],
                'quantity' => $product->quantity_by_product,
            ];
        }

        $this->session = $this->stripe->checkout->sessions->create(
            [
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('payment.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('payment.cancel', []),
            ]
        );
        $this->createPayment($amount, $user_id);
    }

    private function createPayment(int $amount, int $user_id)
    {
        DB::transaction(function () use ($amount, $user_id) {
            Payment::updateOrCreate([
                'amount' => $amount . 00,
                'session_id' => $this->session->id,
                'user_id' => $user_id,
                'payment_method_id' => PaymentMethod::METHODS['CARD'],
                'payment_state_id' => PaymentSate::STATES['UNPAID'],
            ]);
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request)
    {
        $request->validate([
            'session_id' => 'required'
        ]);
        $session_id = $request->get('session_id');
        $session = $this->stripe->checkout->sessions->retrieve($session_id);
        /*  $customer = $stripe->customers->retrieve($session->customer); */
        if (is_null($session)) {
            return response()->json([
                'status' => false,
                'message' => 'error, el pago no se ha podido efectuar',
            ], 500);
        }
        $payment = Payment::where('session_id', $session->id)
            ->where('payment_state_id',  PaymentSate::STATES['UNPAID'])
            ->first();
        if (is_null($payment)) {
            return response()->json([
                'status' => false,
                'message' => 'Pago no encontrado...'
            ], 404);
        }
        DB::transaction(function () use ($payment) {
            $payment->update([
                'payment_state_id' => PaymentSate::STATES['PAID']
            ]);
           /*  ProductOrder::create([
                'payment_id' => $payment->id,
                'order_state_id' => OrderState::STATES['PROCESSED']
            ]); */
        });
        return response()->json([
            'status' => true,
            'message' => '¡Ya es tuyo! Pago realizado con éxito...',
            $session
        ], 200);
    }
}
