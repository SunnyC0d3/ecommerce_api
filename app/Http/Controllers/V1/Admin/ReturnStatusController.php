<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderReturnStatus;
use App\Traits\V1\ApiResponses;

class ReturnStatusController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function edit(OrderReturnStatus $orderReturnStatus) {}

    public function update(Request $request, OrderReturnStatus $orderReturnStatus) {}

    public function destroy(OrderReturnStatus $orderReturnStatus) {}
}
