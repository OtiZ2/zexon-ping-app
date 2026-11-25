<?php

namespace Tests\Feature;

use App\Models\Device;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_device_create()
    {

        $body = [
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Senzor',
        ];

        $response = $this->postJson('/api/devices', $body);

        $response->assertCreated()
            ->assertExactJson([
                'id' => 1,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Senzor',
            ]);

        $this->assertDatabaseHas('devices', $body);
    }

    public function test_store_ping_for_device()
    {

        $device = Device::factory()->create();
        $body = [
            'battery_percent' => 85,
        ];
        $response = $this->postJson("/api/devices/{$device->uuid}/ping", $body);

        $response->assertCreated()
            ->assertExactJson([
                'status' => 'ok',
            ]);

        $this->assertDatabaseHas('pings', [
            'device_id' => $device->id,
            'battery_percent' => 85,
        ]);
    }

    #[DataProvider('invalidDevicePayloads')]
    public function test_device_create_returns_422_for_invalid_payload(array $body, array $errors): void
    {
        $response = $this->postJson('/api/devices', $body);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors($errors);

        $this->assertDatabaseCount('devices', 0);
    }

    /** @return array<string, array{array{uuid?: string, name?: string|int}, array<string, string>}> */
    public static function invalidDevicePayloads(): array
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        return [
            'missing required fields' => [[], [
                'uuid' => 'The uuid field is required.',
                'name' => 'The name field is required.',
            ]],
            'malformed UUID' => [['uuid' => 'invalid-uuid', 'name' => 'Senzor'], [
                'uuid' => 'The uuid field must be a valid UUID.',
            ]],
            'non-string name' => [['uuid' => $uuid, 'name' => 123], [
                'name' => 'The name field must be a string.',
            ]],
            'name over 255 characters' => [['uuid' => $uuid, 'name' => str_repeat('a', 256)], [
                'name' => 'The name field must not be greater than 255 characters.',
            ]],
        ];
    }

    public function test_device_create_returns_422_for_duplicate_uuid(): void
    {
        $device = Device::factory()->create();

        $response = $this->postJson('/api/devices', [
            'uuid' => $device->uuid,
            'name' => 'Senzor',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['uuid' => 'The uuid has already been taken.']);

        $this->assertDatabaseCount('devices', 1);
    }

    /** @param array{battery_percent?: int|float} $body */
    #[TestWith([[], 'The battery percent field is required.'], 'missing percentage')]
    #[TestWith([['battery_percent' => 85.5], 'The battery percent field must be an integer.'], 'fractional percentage')]
    #[TestWith([['battery_percent' => -1], 'The battery percent field must be between 0 and 100.'], 'below zero')]
    #[TestWith([['battery_percent' => 101], 'The battery percent field must be between 0 and 100.'], 'above 100')]
    public function test_store_ping_returns_422_for_invalid_battery_percent(array $body, string $message): void
    {
        $device = Device::factory()->create();

        $response = $this->postJson("/api/devices/{$device->uuid}/ping", $body);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['battery_percent' => $message]);

        $this->assertDatabaseCount('pings', 0);
    }

    public function test_store_ping_returns_422_for_invalid_route_uuid(): void
    {
        $response = $this->postJson('/api/devices/invalid-uuid/ping', ['battery_percent' => 85]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['uuid' => 'The uuid field must be a valid UUID.']);

        $this->assertDatabaseCount('pings', 0);
    }
}
