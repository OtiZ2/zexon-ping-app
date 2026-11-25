<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDeviceRequest extends FormRequest
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
        return $this->json()->all();
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'unique:devices'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
