<?php

namespace App\Http\Requests;

use App\Models\Layup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Layup $layup */
        $layup = $this->route('layup');

        return [
            'layer_order' => [
                'required',
                'integer',
                Rule::unique('layers', 'layer_order')->where(fn ($q) => $q->where('layup_id', $layup->layup_id)),
            ],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric', 'min:-360', 'max:360'],
            'grade' => ['nullable', 'string', 'max:32'],
        ];
    }
}
