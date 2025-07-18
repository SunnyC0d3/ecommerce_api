<?php

namespace App\Services\V1\Orders\Refunds;

use App\Models\Order;
use App\Models\OrderItem;

interface PaymentGatewayRefundInterface
{
    public function refund(Order $order, ?OrderItem $orderItem = null): bool;
}
