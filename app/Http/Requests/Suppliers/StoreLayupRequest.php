<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return $this->user()?->can('create', [Layup::class, $supplier]) ?? false;
    }

    public function rules(): array
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clt_layups', 'name')->where('supplier_id', $supplier->id),
            ],
        ];
    }
}
