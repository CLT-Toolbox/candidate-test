<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Supplier;
use App\Services\Suppliers\SupplierImportService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreviewSupplierImportRequest extends FormRequest
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
            'import_file' => ['required', 'file', 'max:2048'],
            'conflict_strategy' => [
                'required',
                'string',
                Rule::in([
                    SupplierImportService::STRATEGY_MANUAL,
                    SupplierImportService::STRATEGY_OVERWRITE,
                    SupplierImportService::STRATEGY_SKIP,
                    SupplierImportService::STRATEGY_REJECT,
                ]),
            ],
        ];
    }
}
