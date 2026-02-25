<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CltLayupImportResolveRequest extends FormRequest
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
            'conflicts' => 'present|array',
            'conflicts.*.layup_name' => 'required|string',
            'conflicts.*.layer_id' => 'required|integer|exists:clt_layers,id',
            'conflicts.*.order' => 'required|integer',
            'conflicts.*.resolution' => 'required|string|in:keep,overwrite',
            'conflicts.*.incoming' => 'required|array',
            'conflicts.*.incoming.thickness' => 'required|numeric',
            'conflicts.*.incoming.width' => 'required|numeric',
            'conflicts.*.incoming.angle' => 'required|numeric',
            'clean_data' => 'present|json',
        ];
    }
}
