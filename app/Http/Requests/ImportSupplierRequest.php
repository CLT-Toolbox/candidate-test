<?php

namespace App\Http\Requests;

use App\Services\SupplierImportService;
use Illuminate\Foundation\Http\FormRequest;

class ImportSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:json,txt'],
            'strategy' => [
                'sometimes',
                'string',
                'in:' . implode(',', [
                    SupplierImportService::STRATEGY_SKIP,
                    SupplierImportService::STRATEGY_OVERWRITE,
                    SupplierImportService::STRATEGY_DUPLICATE,
                    SupplierImportService::STRATEGY_REJECT,
                ]),
            ],
        ];
    }
}
