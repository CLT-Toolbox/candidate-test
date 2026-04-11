<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;

class ResolveSupplierImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return $this->user()?->can('import', $supplier) ?? false;
    }

    public function rules(): array
    {
        return [
            'resolutions' => ['required', 'array', 'min:1'],
            'resolutions.*' => ['required', 'in:keep_existing,accept_incoming'],
        ];
    }
}
