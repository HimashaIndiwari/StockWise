{{-- Destructive action form guarded by the shared confirmation modal.
     Pass $message as a plain string; it is escaped once here and shown via textContent. --}}
@props(['action', 'message', 'method' => 'DELETE', 'buttonClass' => 'btn btn-sm btn-outline-danger', 'label' => 'Delete', 'labelClass' => 'ms-1', 'icon' => 'trash'])
<form method="POST" action="{{ $action }}" data-confirm="{{ $message }}" {{ $attributes->class('d-inline') }}>
    @csrf
    @method($method)
    <button type="submit" class="{{ $buttonClass }}">
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i><span class="{{ $labelClass }}">{{ $label }}</span>
    </button>
</form>
