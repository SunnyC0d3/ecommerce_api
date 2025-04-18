<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Traits\V1\ApiResponses;

class OrderStatusController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function edit(OrderStatus $orderStatus) {}

    public function update(Request $request, OrderStatus $orderStatus) {}

    public function destroy(OrderStatus $orderStatus) {}
}
