<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncLayupLayersRequest extends FormRequest
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
            'layers' => ['required', 'array', 'min:1'],
            'layers.*.thickness' => ['required', 'numeric', 'min:0'],
            'layers.*.width' => ['required', 'numeric', 'min:0'],
            'layers.*.angle' => ['required', 'numeric', 'min:-360', 'max:360'],
            'layers.*.grade' => ['nullable', 'string', 'max:32'],
        ];
    }
}
