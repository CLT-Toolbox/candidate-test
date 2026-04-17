<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $layup = $this->route('layup');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('layups', 'name')
                    ->where('supplier_id', $layup?->supplier_id)
                    ->ignore($layup?->getKey()),
            ],
        ];
    }
}
