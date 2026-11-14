<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Validation for editing a product. Quantity is deliberately absent:
 * stock only changes through auditable stock movements (StockService).
 */
class UpdateProductRequest extends StoreProductRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        $rules = parent::rules();

        unset($rules['quantity']);

        $rules['sku'] = [
            'required', 'string', 'max:100', 'regex:/^[A-Z0-9][A-Z0-9\-_.\/]*$/',
            Rule::unique('products', 'sku')->ignore($this->route('product')),
        ];
        $rules['remove_image'] = ['nullable', 'boolean'];

        return $rules;
    }
}
