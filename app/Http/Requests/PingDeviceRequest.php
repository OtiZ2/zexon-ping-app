<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PingDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return ['uuid' => $this->route('uuid')] + $this->json()->all();
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid'],
            'battery_percent' => ['required', 'integer', 'between:0,100'],
        ];
    }
}
