{{-- Shared create/edit form fields. Expects $product, $categories, $suppliers. --}}
@php($isEdit = $product->exists)
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h2 class="card-title h6 mb-0">Product details</h2></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <x-form.input name="name" label="Product name" :value="$product->name" required maxlength="255" />
                    </div>
                    <div class="col-md-4">
                        <x-form.input name="sku" label="SKU / product code" :value="$product->sku" required maxlength="100"
                                      help="Unique. Letters, numbers, - _ . /" style="text-transform: uppercase" />
                    </div>
                </div>
                <x-form.textarea name="description" label="Description" :value="$product->description" rows="3" />
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="category_id" label="Category" :options="$categories" :value="$product->category_id"
                                       placeholder="Select a category" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.select name="supplier_id" label="Supplier" :options="$suppliers" :value="$product->supplier_id"
                                       placeholder="No supplier" help="Optional." />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h2 class="card-title h6 mb-0">Pricing &amp; stock</h2></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="purchase_price" type="number" label="Purchase price" :value="$product->purchase_price"
                                      required min="0" step="0.01" inputmode="decimal" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="selling_price" type="number" label="Selling price" :value="$product->selling_price"
                                      required min="0" step="0.01" inputmode="decimal" />
                    </div>
                    @unless ($isEdit)
                        <div class="col-md-6">
                            <x-form.input name="quantity" type="number" label="Initial quantity" value="0" min="0" step="1"
                                          help="Recorded as a 'Stock IN' movement for the audit history." />
                        </div>
                    @endunless
                    <div class="col-md-6">
                        <x-form.input name="min_stock_level" type="number" label="Minimum stock level"
                                      :value="$product->min_stock_level ?? 5" required min="0" step="1"
                                      help="Products at or below this level are flagged as low stock." />
                    </div>
                </div>
                @if ($isEdit)
                    <div class="alert alert-light border small mb-0">
                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                        Current quantity is <strong>{{ $product->quantity }}</strong>. To change stock, use
                        <strong>Stock IN / OUT / Adjustment</strong> so that every change is recorded.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h2 class="card-title h6 mb-0">Image</h2></div>
            <div class="card-body">
                @if ($isEdit && $product->imageUrl())
                    <img src="{{ $product->imageUrl() }}" alt="Current image of {{ $product->name }}" class="product-image mb-3">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image" @checked(old('remove_image'))>
                        <label class="form-check-label" for="remove_image">Remove current image</label>
                    </div>
                @endif
                <x-form.input name="image" type="file" label="{{ $isEdit && $product->image ? 'Replace image' : 'Upload image' }}"
                              accept="image/jpeg,image/png,image/webp" help="Optional. JPG, PNG or WebP, max 2 MB." />
            </div>
        </div>
    </div>
</div>
