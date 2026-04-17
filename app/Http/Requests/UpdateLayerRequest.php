<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $layer = $this->route('layer');

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('layers', 'layer_order')
                    ->where('layup_id', $layer?->layup_id)
                    ->ignore($layer?->getKey()),
            ],
            'thickness' => ['required', 'numeric'],
            'width' => ['required', 'numeric'],
            'angle' => ['required', 'numeric'],
        ];
    }
}
