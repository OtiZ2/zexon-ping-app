<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'last_ping' => $this->lastPing?->created_at?->toISOString(),
            'last_battery_percent' => $this->lastPing?->battery_percent,
        ];
    }
}
