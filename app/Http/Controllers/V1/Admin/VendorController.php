<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\V1\ApiResponses;

class VendorController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show(Vendor $vendor) {}

    public function edit(Vendor $vendor) {}

    public function update(Request $request, Vendor $vendor) {}

    public function destroy(Vendor $vendor) {}
}
