<?php

namespace App\Http\Requests\Suppliers;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Layup $layup */
        $layup = $this->route('layup');

        return $this->user()?->can('update', $layup) ?? false;
    }

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
                Rule::unique('clt_layups', 'name')
                    ->where('supplier_id', $supplier->id)
                    ->ignore($layup),
            ],
        ];
    }
}
