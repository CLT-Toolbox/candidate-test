<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return $this->user()?->can('update', $supplier) ?? false;
    }

    public function rules(): array
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplier)],
        ];
    }
}
