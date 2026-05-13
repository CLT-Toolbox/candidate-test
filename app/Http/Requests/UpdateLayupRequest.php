<?php

namespace App\Http\Requests;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLayupRequest extends FormRequest
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
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        /** @var Layup $layup */
        $layup = $this->route('layup');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('layups', 'name')
                    ->where('supplier_id', $supplier->id)
                    ->ignore($layup->id),
            ],
            'description' => ['nullable', 'string'],
        ];
    }
}
