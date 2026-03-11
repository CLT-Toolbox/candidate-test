<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'layup_id' => ['required', 'exists:layups,id'],
            'layer_order' => ['required', 'integer', 'min:1'],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric', 'between:-360,360'],
        ];
    }
}
