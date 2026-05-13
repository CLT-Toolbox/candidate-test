<?php

namespace App\Http\Requests;

use App\Services\SupplierImportExportService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ImportSupplierRequest extends FormRequest
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
        return [
            'strategy' => [
                'required',
                'in:'.implode(',', [
                    SupplierImportExportService::STRATEGY_OVERWRITE,
                    SupplierImportExportService::STRATEGY_SKIP,
                    SupplierImportExportService::STRATEGY_DUPLICATE_LAYUP,
                    SupplierImportExportService::STRATEGY_REJECT,
                ]),
            ],
            'payload' => ['nullable', 'string', 'max:1048576', 'required_without:file'],
            'file' => ['nullable', 'file', 'mimes:json,txt', 'max:10240', 'required_without:payload'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $content = $this->payloadContent();
            if ($content === '') {
                return;
            }

            json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $validator->errors()->add('payload', 'Invalid JSON format in payload/file.');
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function payloadAsArray(): array
    {
        $content = $this->payloadContent();

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function payloadContent(): string
    {
        if ($this->hasFile('file')) {
            $content = file_get_contents($this->file('file')->getRealPath());

            return is_string($content) ? trim($content) : '';
        }

        return trim((string) ($this->input('payload') ?? ''));
    }
}
