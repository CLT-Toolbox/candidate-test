<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'strategy' => ['nullable', 'in:overwrite,skip,duplicate_layup,reject'],
            'layups' => ['required', 'array', 'min:1'],
            'layups.*.name' => ['required', 'string', 'max:255'],
            'layups.*.layers' => ['required', 'array', 'min:1'],
            'layups.*.layers.*.layer_order' => ['required', 'integer', 'min:1'],
            'layups.*.layers.*.thickness' => ['required', 'numeric'],
            'layups.*.layers.*.width' => ['required', 'numeric'],
            'layups.*.layers.*.angle' => ['required', 'numeric'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('layups', []) as $layupIndex => $layup) {
                $orders = collect($layup['layers'] ?? [])->pluck('layer_order');

                if ($orders->count() === $orders->unique()->count()) {
                    continue;
                }

                $validator->errors()->add(
                    "layups.{$layupIndex}.layers",
                    'Layer order values must be unique within each layup payload.'
                );
            }
        });
    }
}
