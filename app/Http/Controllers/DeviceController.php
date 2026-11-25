<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }
}
