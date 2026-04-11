<?php

namespace App\Http\Requests\CltLayer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCltLayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'layer_order' => ['required', 'integer', 'min:1'],
            'thickness'   => ['required', 'numeric', 'min:0'],
            'width'       => ['required', 'numeric', 'min:0'],
            'angle'       => ['required', 'numeric'],
        ];
    }
}
