<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportLayupsRequest extends FormRequest
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
        return [
            'file' => ['required', 'file', 'mimes:csv,json', 'max:10240'],
            'conflict_strategy' => ['nullable', 'in:skip,overwrite,duplicate,reject'],
            'dry_run' => ['nullable', 'boolean'],
            'conflict_resolutions' => ['nullable', 'json'],
        ];
    }
}
