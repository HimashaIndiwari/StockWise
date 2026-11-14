<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for recording a stock movement (plan §10).
 * user_id, previous_quantity and new stock level are never taken from the client:
 * StockService computes them on the locked product row.
 * Product-level authorization is enforced in the controller and again inside StockService.
 */
class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // record-level check (manageStock) happens once the product is resolved
    }

    public function rules(): array
    {
        return [
            // Must exist and not be archived.
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'type' => ['required', 'string', Rule::in(StockMovement::TYPES)],
            // IN / OUT: units moved (positive). ADJUSTMENT: counted stock level (>= 0).
            'quantity' => [
                'required', 'integer', 'max:1000000',
                Rule::when($this->input('type') === StockMovement::TYPE_ADJUSTMENT, ['min:0'], ['min:1']),
            ],
            'reason' => [
                Rule::requiredIf($this->input('type') === StockMovement::TYPE_ADJUSTMENT),
                'nullable', 'string', 'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'The selected product does not exist or has been archived.',
            'quantity.min' => $this->input('type') === StockMovement::TYPE_ADJUSTMENT
                ? 'The counted quantity cannot be negative.'
                : 'The quantity must be at least 1.',
            'reason.required' => 'A reason is required for stock adjustments.',
        ];
    }

    public function attributes(): array
    {
        return ['product_id' => 'product', 'type' => 'movement type'];
    }
}
