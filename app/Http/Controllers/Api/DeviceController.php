<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDeviceRequest;
use App\Http\Requests\PingDeviceRequest;
use App\Http\Resources\DeviceDetailResource;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(DeviceResource::collection(Device::all()));
    }

    public function show(Device $device): JsonResponse
    {
        return response()->json(DeviceDetailResource::make($device));
    }

    public function create(CreateDeviceRequest $request): JsonResponse
    {
        $device = Device::query()->createOrFirst(
            ['uuid' => $request->validated('uuid')],
            ['name' => $request->validated('name')],
        );

        return response()->json(
            $device->only(['id', 'uuid', 'name']),
            JsonResponse::HTTP_CREATED
        );

    }

    public function ping(PingDeviceRequest $request, string $uuid): JsonResponse
    {
        $device = Device::query()->where('uuid', $uuid)->firstOrFail();
        $device->pings()->create($request->safe()->only(['battery_percent']));

        return response()->json([
            'status' => 'ok',
        ], JsonResponse::HTTP_CREATED);
    }
}
