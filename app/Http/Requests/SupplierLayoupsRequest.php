<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SupplierLayoupsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'layers' => 'required|array|min:1',
            'layers.*.layer_order' => 'required|integer|min:1',
            'layers.*.thickness' => 'required|numeric|min:0',
            'layers.*.width' => 'required|numeric|min:0',
            'layers.*.angle' => 'required|numeric|min:0|max:360',
        ];
    }
}
