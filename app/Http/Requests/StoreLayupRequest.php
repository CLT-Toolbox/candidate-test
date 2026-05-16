<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLayupRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thickness' => ['nullable', 'string'],
            'grade' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:ACTIVE,DRAFT,ARCHIVED'],
        ];
    }
}
