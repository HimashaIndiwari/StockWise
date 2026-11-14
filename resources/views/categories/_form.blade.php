<div class="card" style="max-width: 720px">
    <div class="card-body">
        <x-form.input name="name" label="Category name" :value="$category->name" required maxlength="255" autofocus />
        <x-form.textarea name="description" label="Description" :value="$category->description" rows="3" help="Optional." />
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1" aria-hidden="true"></i> {{ $category->exists ? 'Save changes' : 'Create category' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light border">Cancel</a>
    </div>
</div>
