<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['nullable', 'string', 'max:255'],
            'width'             => ['sometimes', 'required', 'numeric', 'min:0.1', 'max:100'],
            'length'            => ['sometimes', 'required', 'numeric', 'min:0.1', 'max:100'],
            'height'            => ['sometimes', 'required', 'numeric', 'min:0.1', 'max:20'],
            'floor_material'    => ['nullable', 'string', 'max:100'],
            'floor_color'       => ['nullable', 'string', 'max:50'],
            'wall_material'     => ['nullable', 'string', 'max:100'],
            'wall_color'        => ['nullable', 'string', 'max:50'],
            'lighting_settings' => ['nullable', 'array'],
        ];
    }
}
