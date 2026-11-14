{{-- Movement type + quantity + reason fields. Shared by the full form and the product-page panel. --}}
@php($selectedType = old('type', \App\Models\StockMovement::TYPE_IN))
<fieldset class="mb-3 border-0 p-0">
    <legend class="form-label fs-6 mb-2">Movement type <span class="text-danger" aria-hidden="true">*</span></legend>
    <div class="d-flex flex-wrap gap-2">
        @foreach ([
            \App\Models\StockMovement::TYPE_IN => ['Stock IN', 'box-arrow-in-down', 'btn-outline-success'],
            \App\Models\StockMovement::TYPE_OUT => ['Stock OUT', 'box-arrow-up', 'btn-outline-danger'],
            \App\Models\StockMovement::TYPE_ADJUSTMENT => ['Adjustment', 'sliders', 'btn-outline-primary'],
        ] as $value => [$label, $icon, $class])
            <input type="radio" class="btn-check" name="type" id="{{ $idPrefix }}type-{{ $value }}" value="{{ $value }}" autocomplete="off" @checked($selectedType === $value)>
            <label class="btn {{ $class }}" for="{{ $idPrefix }}type-{{ $value }}"><i class="bi bi-{{ $icon }} me-1" aria-hidden="true"></i>{{ $label }}</label>
        @endforeach
    </div>
    @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</fieldset>

<x-form.input name="quantity" type="number" label="Quantity" min="0" step="1" required :id="$idPrefix.'quantity'"
              help="Stock IN / OUT: number of units moved. Adjustment: the new counted stock level." />

<x-form.textarea name="reason" label="Reason / notes" rows="2" :id="$idPrefix.'reason'" maxlength="1000"
                 help="Required for adjustments (e.g. 'Stock count correction')." />
