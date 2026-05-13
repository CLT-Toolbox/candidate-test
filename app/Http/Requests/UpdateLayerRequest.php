<?php

namespace App\Http\Requests;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLayerRequest extends FormRequest
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
        /** @var Layup $layup */
        $layup = $this->route('layup');

        /** @var Layer $layer */
        $layer = $this->route('layer');

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('layers', 'layer_order')
                    ->where('layup_id', $layup->id)
                    ->ignore($layer->id),
            ],
            'thickness' => ['required', 'numeric', 'gt:0'],
            'width' => ['required', 'numeric', 'gt:0'],
            'angle' => ['required', 'numeric', 'between:-90,90'],
        ];
    }
}
