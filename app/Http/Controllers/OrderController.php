<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Rules\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class OrderController extends Controller
{

    private $orders;
    private $cart;
    private $order;

    public function getCustomerOrders(Request $request) {
            $checkoutSessionId = $request->query('checkout_session_id', null);

            if($checkoutSessionId) {
                $this->order = $this->getOrderByCheckoutId($checkoutSessionId);
            }
    
            if($this->order) {
                $this->acceptThisOrder();
            }
            
            return $this->getFormattedOrders();
    }

    private function getOrderByCheckoutId($checkoutId)
    {
        return Order::where('checkout_session_id', $checkoutId)->firstWhere('status', 'unfinished');
    }

    private function acceptThisOrder()
    {
        $this->order->status = "food processing";
        $this->order->save();
    }

    private function getFormattedOrders()
    {
        return Auth::user()->orders
            ->where('status', '!=', 'unfinished')
            ->get()
            ->map(fn($order) => $this->formatOrderForResponse($order));
    }

    private function formatOrderForResponse($order)
    {
        return [
            'id' => $order->id,
            'description' => $order->dishes->map(fn($dish) => "{$dish->name} x {$dish->pivot->quantity}")->implode(', '),
            'montant' => $order->montant,
            'numberOfItems' => $order->numberOfItems,
            'status' => $order->status
        ];
    }

    public function store(StoreOrderRequest $request)
    {
        $session = $this->checkout();

        $this->cart = Auth::user()?->cart;

        if(!$this->cart || $this->cart->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 404);
        }

        $this->order = Order::create([
            ...$request->all(),
            ...$this->getOrderExtraData(),
            ...["checkout_session_id" => $session->id]
        ]);

        $this->transfertCartToOrder();

        return response()->json([
            'status' => 'Order created successfully',
            'url' => $session->url
        ], 200);
    }

    private function getOrderExtraData()
    {
        return [
            'user_id' => Auth::id(),
            'montant' => $this->getOrderMontant(),
            'numberOfItems' => $this->cart->first()->dishes->count(),
            'status' => 'unfinished',
        ];
    }

    private function getOrderMontant()
    {
        $SHIPPING_COST = 39;
        return $this->cart->dishes
                ->sum(fn($dish) => $dish->pivot->total) + $SHIPPING_COST;
    }

    private function transfertCartToOrder()
    {
        $this->cart->dishes->each(function($dish) {
            $this->order->dishes()->attach($dish->id, [
                'quantity' => $dish->pivot->quantity,
                'total' => $dish->pivot->total
            ]);
        });

        $this->cart->delete();
    }

    public function checkout()
    {
        $this->cart = Auth::user()->cart;

        Stripe::setApiKey(config('stripe.sk'));

        $session = $this->getCheckoutSession();

        return $session;
    }

    private function getCheckoutSession()
    {
        return StripeSession::create([
            'mode' => 'payment',
            'line_items' => $this->getCartItemsForCheckout(),
            'success_url' => $this->getSuccessUrl(),
            'cancel_url' => $this->getCancelUrl(),
        ]);
    }

    private function getSuccessUrl()
    {
        return env('FRONTEND_URL').'/orders?checkout_session_id={CHECKOUT_SESSION_ID}';
    }

    private function getCancelUrl()
    {
        return env('FRONTEND_URL').'/';
    }

    private function getCartItemsForCheckout()
    {
        return $this->cart->dishes->map(function($item) {
            return $this->getCartItemForCheckout($item);
        })->toArray();
    }

    private function getCartItemForCheckout($item)
    {
        return [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $item->name,
                ],
                'unit_amount' => $item->price * 100,
            ],
            'quantity' => $item->pivot->quantity,
        ];
    }

    public function getAllOrders()
    {
        //
    }

    public function modifyOrderStatus(Order $order)
    {
        //
    }
}
