{{-- Bootstrap form field with label, old() value and inline validation error. --}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null, 'prepend' => null, 'bag' => 'default'])
@php
    $id = $attributes->get('id', 'field-'.$name);
    $error = $errors->getBag($bag)->first($name);
    $current = in_array($type, ['password', 'file'], true) ? null : old($name, $value);
@endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if ($required)<span class="text-danger" aria-hidden="true"> *</span>@endif
    </label>
    @if ($prepend)<div @class(['input-group', 'has-validation' => $error])><span class="input-group-text">{{ $prepend }}</span>@endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
           @if (! is_null($current)) value="{{ $current }}" @endif
           @if ($required) required @endif
           @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($help) aria-describedby="{{ $id }}-help" @endif
           {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $error]) }}>
    @if ($error)
        <div class="invalid-feedback" id="{{ $id }}-error">{{ $error }}</div>
    @endif
    @if ($prepend)</div>@endif
    @if ($help && ! $error)
        <div class="form-text" id="{{ $id }}-help">{{ $help }}</div>
    @endif
</div>
