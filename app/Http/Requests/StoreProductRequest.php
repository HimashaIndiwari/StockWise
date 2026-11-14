<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for creating a product (plan §10).
 * `created_by` is never accepted from input; it is assigned from the authenticated user.
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('sku'))) {
            $this->merge(['sku' => strtoupper(trim($this->input('sku')))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'regex:/^[A-Z0-9][A-Z0-9\-_.\/]*$/', Rule::unique('products', 'sku')],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'supplier_id' => ['nullable', 'integer', Rule::exists('suppliers', 'id')],
            'purchase_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'selling_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'quantity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'min_stock_level' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.regex' => 'The SKU may contain only letters, numbers, dashes, underscores, dots and slashes.',
            'sku.unique' => 'This SKU is already used by another product (including archived products).',
            'category_id.exists' => 'The selected category does not exist.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'image.max' => 'The image may not be larger than 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'sku' => 'SKU',
            'category_id' => 'category',
            'supplier_id' => 'supplier',
            'min_stock_level' => 'minimum stock level',
            'quantity' => 'initial quantity',
        ];
    }
}
