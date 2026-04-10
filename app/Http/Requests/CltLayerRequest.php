<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CltLayerRequest extends FormRequest
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
            'layup_id'    => 'required|exists:clt_layups,id',
            'layer_order' => 'required|integer|min:1',
            'thickness'   => 'required|numeric|min:0',
            'width'       => 'required|numeric|min:0',
            'angle'       => 'required|numeric|min:0|max:360',
        ];
    }
}
