{{-- Simple GET search box used by list pages. --}}
@props(['action', 'value' => null, 'placeholder' => 'Search…'])
<form method="GET" action="{{ $action }}" class="d-flex gap-2" role="search">
    <label for="list-search" class="visually-hidden">Search</label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
        <input type="search" id="list-search" name="q" value="{{ $value }}" class="form-control" placeholder="{{ $placeholder }}">
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    @if ($value)
        <a href="{{ $action }}" class="btn btn-light border" title="Clear search">
            <i class="bi bi-x-lg" aria-hidden="true"></i><span class="visually-hidden">Clear search</span>
        </a>
    @endif
</form>
