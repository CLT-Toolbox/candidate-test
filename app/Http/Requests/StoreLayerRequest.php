<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $layupId = $this->route('layup')?->getKey();

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('layers', 'layer_order')->where('layup_id', $layupId),
            ],
            'thickness' => ['required', 'numeric'],
            'width' => ['required', 'numeric'],
            'angle' => ['required', 'numeric'],
        ];
    }
}
