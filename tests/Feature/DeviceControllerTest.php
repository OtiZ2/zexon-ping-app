<?php

namespace Tests\Feature;

use App\Models\Device;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_device_is_saved_with_the_submitted_uuid_and_name(): void
    {
        $device = ['uuid' => '550e8400-e29b-41d4-a716-446655440000', 'name' => 'Sensor'];

        $response = $this->postJson('/api/devices', $device);

        $response->assertCreated()->assertExactJson(['status' => 'create ok', 'body' => $device]);
        $this->assertDatabaseHas('devices', $device);
    }

    public function test_duplicate_device_returns_409_without_changing_the_name(): void
    {
        $device = Device::factory()->create(['name' => 'Sensor']);

        $response = $this->postJson('/api/devices', ['uuid' => $device->uuid, 'name' => 'Different name']);

        $response->assertConflict()->assertJsonValidationErrors('uuid');
        $this->assertDatabaseHas('devices', ['uuid' => $device->uuid, 'name' => 'Sensor']);
        $this->assertDatabaseCount('devices', 1);
    }

    public function test_ping_is_saved_for_the_device_in_the_url(): void
    {
        $device = Device::factory()->create();

        $response = $this->postJson("/api/devices/{$device->uuid}/ping", [
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'battery_percent' => 75,
        ]);

        $response->assertCreated()->assertExactJson([
            'status' => 'ping ok',
            'body' => ['uuid' => $device->uuid, 'battery_percent' => 75],
        ]);
        $this->assertDatabaseHas('pings', ['device_id' => $device->id, 'battery_percent' => 75]);
    }

    public function test_ping_for_an_unknown_device_returns_404(): void
    {
        $response = $this->postJson('/api/devices/550e8400-e29b-41d4-a716-446655440000/ping', [
            'battery_percent' => 75,
        ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('pings', 0);
    }

    public function test_ping_with_battery_above_100_returns_422(): void
    {
        $device = Device::factory()->create();

        $response = $this->postJson("/api/devices/{$device->uuid}/ping", ['battery_percent' => 101]);

        $response->assertUnprocessable()->assertJsonValidationErrors('battery_percent');
        $this->assertDatabaseCount('pings', 0);
    }
}
