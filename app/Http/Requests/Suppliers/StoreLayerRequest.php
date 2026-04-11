<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Layup $layup */
        $layup = $this->route('layup');

        return $this->user()?->can('create', [Layer::class, $layup]) ?? false;
    }

    public function rules(): array
    {
        /** @var Layup $layup */
        $layup = $this->route('layup');

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('clt_layers', 'layer_order')->where('layup_id', $layup->id),
            ],
            'thickness' => ['required', 'numeric', 'gt:0'],
            'width' => ['required', 'numeric', 'gt:0'],
            'angle' => ['required', 'numeric', 'between:-360,360'],
        ];
    }
}
