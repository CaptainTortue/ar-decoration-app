<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectObjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'position_x' => ['nullable', 'numeric'],
            'position_y' => ['nullable', 'numeric'],
            'position_z' => ['nullable', 'numeric'],
            'rotation_x' => ['nullable', 'numeric'],
            'rotation_y' => ['nullable', 'numeric'],
            'rotation_z' => ['nullable', 'numeric'],
            'scale_x'    => ['nullable', 'numeric', 'min:0.001'],
            'scale_y'    => ['nullable', 'numeric', 'min:0.001'],
            'scale_z'    => ['nullable', 'numeric', 'min:0.001'],
            'color'      => ['nullable', 'string', 'max:50'],
            'material'   => ['nullable', 'string', 'max:100'],
            'is_locked'  => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'boolean'],
        ];
    }
}
