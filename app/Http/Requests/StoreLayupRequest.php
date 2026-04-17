<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->getKey();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('layups', 'name')->where('supplier_id', $supplierId),
            ],
        ];
    }
}
