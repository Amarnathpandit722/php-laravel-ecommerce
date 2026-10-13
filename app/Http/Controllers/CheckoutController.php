<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
    ) {}

    /**
     * Show checkout page.
     * - If cart empty → redirect to /cart with a message.
     * - If no serviceable address → redirect to /addresses.
     */
    public function index()
    {
        $userId = auth()->id();

        $cart = Cart::where('user_id', $userId)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $addresses = Address::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        $serviceableAddresses = $addresses->where('is_serviceable', true);

        return view('checkout.index', [
            'cart'         => $cart,
            'addresses'    => $addresses,
            'serviceable'  => $serviceableAddresses,
            'razorpayKey'  => config('services.razorpay.key'),
        ]);
    }

    /**
     * Create a pending order + a Razorpay order → return JSON for the frontend.
     */
    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
        ]);

        try {
            $order = $this->orderService->createFromCart(auth()->id(), $data['address_id']);
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        // Create Razorpay order
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $razorpayOrder = $api->order->create([
                'receipt'  => $order->order_number,
                'amount'   => $order->total,      // already in paise
                'currency' => $order->currency,
                'notes'    => [
                    'order_id' => (string) $order->id,
                    'user_id'  => (string) auth()->id(),
                ],
            ]);

            $order->update([
                'razorpay_order_id' => $razorpayOrder['id'],
                'payment_method'    => 'razorpay',
            ]);

            return response()->json([
                'ok' => true,
                'order' => [
                    'id'           => $order->id,
                    'number'       => $order->order_number,
                    'amount'       => $order->total,
                    'currency'     => $order->currency,
                ],
                'razorpay' => [
                    'key'       => config('services.razorpay.key'),
                    'order_id'  => $razorpayOrder['id'],
                    'amount'    => $razorpayOrder['amount'],
                    'currency'  => $razorpayOrder['currency'],
                ],
            ]);
        } catch (\Throwable $e) {
            $this->orderService->markFailed($order, $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Could not start payment. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Verify Razorpay signature and mark order as paid.
     */
    public function verifyPayment(Request $request)
    {
        $data = $request->validate([
            'order_id'             => ['required', 'integer', 'exists:orders,id'],
            'razorpay_order_id'    => ['required', 'string'],
            'razorpay_payment_id'  => ['required', 'string'],
            'razorpay_signature'   => ['required', 'string'],
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($data['order_id']);

        // Verify signature
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature'],
            ]);
        } catch (\Throwable $e) {
            $this->orderService->markFailed($order, 'Signature verification failed: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Payment verification failed.'], 400);
        }

        // Fetch payment details (optional, for audit)
        try {
            $payment = $api->payment->fetch($data['razorpay_payment_id'])->toArray();
        } catch (\Throwable $e) {
            $payment = [];
        }

        $this->orderService->markPaid($order, [
            'razorpay_order_id'   => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
            'meta'                => $payment,
        ]);

        return response()->json([
            'ok'       => true,
            'redirect' => route('checkout.success', $order->order_number),
        ]);
    }

    /**
     * Success page.
     */
    public function success(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        return view('checkout.success', compact('order'));
    }

    /**
     * Failure page.
     */
    public function failure(Request $request)
    {
        return view('checkout.failure', [
            'reason' => $request->query('reason', 'Payment was not completed.'),
        ]);
    }
}
