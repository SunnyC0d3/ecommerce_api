<?php

namespace App\Services\V1\Payments;

use App\Constants\OrderStatuses;
use App\Constants\PaymentMethods;
use App\Constants\PaymentStatuses;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Payment as DB;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Traits\V1\ApiResponses;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Customer;

class StripePayment implements PaymentHandlerInterface
{
    use ApiResponses;

    private $secret;

    public function __construct()
    {
        $this->secret = config('services.stripe_secret');
        Stripe::setApiKey($this->secret);
    }

    public function createPayment(Request $request)
    {
        $data = $request->validated();

        $order = Order::with(['orderItems.product', 'user'])->findOrFail($data['order_id']);
        $paymentMethod = PaymentMethod::where('name', PaymentMethods::STRIPE)->firstOrFail();

        $existingPayment = DB::where('order_id', $order->id)
            ->where('payment_method_id', $paymentMethod->id)
            ->first();

        if ($existingPayment) {
            if ($existingPayment->status === PaymentStatuses::PAID) {
                return $this->ok('Payment has already been processed for this order.');
            }

            $intent = PaymentIntent::retrieve($existingPayment->transaction_reference);

            return $this->ok('Existing PaymentIntent retrieved.', [
                'client_secret' => $intent->client_secret,
            ]);
        }

        if (!$order->user->stripe_customer_id) {
            $customer = Customer::create($this->getUserDetails($order->user));
            $order->user->stripe_customer_id = $customer->id;
            $order->user->save();
        } else {
            $customer = Customer::retrieve($order->user->stripe_customer_id);
        }

        $amountInPennies = $order->getTotalAmountInPennies();

        Log::info('Creating Stripe PaymentIntent', [
            'order_id' => $order->id,
            'amount_pennies' => $amountInPennies,
            'amount_pounds' => $amountInPennies / 100,
            'customer_id' => $customer->id
        ]);

        $intent = PaymentIntent::create([
            'amount' => $amountInPennies,
            'currency' => 'gbp',
            'automatic_payment_methods' => [
                'enabled' => true
            ],
            'customer' => $customer->id,
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $order->user->id
            ]
        ]);

        DB::create([
            'order_id' => $order->id,
            'user_id' => $order->user->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => $amountInPennies,
            'status' => PaymentStatuses::PENDING,
            'processed_at' => now(),
            'transaction_reference' => $intent->id,
        ]);

        Log::info('Payment record created', [
            'order_id' => $order->id,
            'payment_intent_id' => $intent->id,
            'amount_pennies' => $amountInPennies,
            'amount_pounds' => $amountInPennies / 100
        ]);

        return $this->ok('PaymentIntent created successfully.', [
            'client_secret' => $intent->client_secret,
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $paymentIntentId = $request->input('payment_intent_id');
        $orderId = $request->input('order_id');

        $payment = DB::where('transaction_reference', $paymentIntentId)->first();

        if (!$payment) {
            Log::warning('Payment verification failed: Payment not found', [
                'payment_intent_id' => $paymentIntentId,
                'order_id' => $orderId
            ]);
            return $this->error('Payment not found', 404);
        }

        Stripe::setApiKey(config('services.stripe_secret'));
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

        Log::info('Verifying payment', [
            'payment_intent_id' => $paymentIntentId,
            'stripe_status' => $paymentIntent->status,
            'stripe_amount_pennies' => $paymentIntent->amount,
            'local_amount_pennies' => $payment->getAmountInPennies(),
            'amounts_match' => $payment->matchesStripeAmount($paymentIntent->amount)
        ]);

        if ($paymentIntent->status === 'succeeded' && $payment->status !== PaymentStatuses::PAID) {
            if (!$payment->matchesStripeAmount($paymentIntent->amount)) {
                Log::error('Payment amount mismatch during verification', [
                    'payment_intent_id' => $paymentIntentId,
                    'stripe_amount_pennies' => $paymentIntent->amount,
                    'local_amount_pennies' => $payment->getAmountInPennies()
                ]);
                return $this->error('Payment amount mismatch', 400);
            }

            $order = $payment->order;

            $payment->status = PaymentStatuses::PAID;
            $payment->processed_at = now();
            $payment->response_payload = json_encode($paymentIntent);
            $payment->save();

            $order->status_id = OrderStatus::where('name', OrderStatuses::CONFIRMED)->value('id');
            $order->save();

            Log::info('Payment verified and updated via API', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'amount_pennies' => $payment->getAmountInPennies()
            ]);
        }

        return $this->ok('Payment verified', [
            'payment_status' => $payment->status,
            'order_status' => $payment->order->status->name ?? 'Unknown'
        ]);
    }

    private function getProductDetails(Collection $orderItems)
    {
        $products = [];

        foreach ($orderItems as $item) {
            $products[] = [
                'product_id' => $item->product->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'price_pennies' => $item->getPriceInPennies(),
                'price_pounds' => $item->getPriceInPounds(),
            ];
        }

        return $products;
    }

    private function getUserDetails(User $user)
    {
        $userDetails = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        if ($user->userAddress) {
            $userDetails['address'] = [
                'line1' => $user->userAddress->address_line1,
                'line2' => $user->userAddress->address_line2,
                'city' => $user->userAddress->city,
                'postal_code' => $user->userAddress->postal_code,
                'country' => $user->userAddress->country,
            ];
        }

        return $userDetails;
    }
}
