@foreach (['success' => 'check-circle', 'error' => 'x-octagon', 'warning' => 'exclamation-triangle'] as $type => $icon)
    @if (session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-{{ $icon }} mt-1" aria-hidden="true"></i>
            <div>{{ session($type) }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
        <i class="bi bi-exclamation-circle mt-1" aria-hidden="true"></i>
        <div>{{ $errors->first('general') ?: 'Please correct the highlighted errors and try again.' }}</div>
    </div>
@endif
