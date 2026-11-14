@props(['name', 'label', 'value' => null, 'required' => false, 'rows' => 3, 'help' => null])
@php
    $id = $attributes->get('id', 'field-'.$name);
    $error = $errors->first($name);
@endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if ($required)<span class="text-danger" aria-hidden="true"> *</span>@endif
    </label>
    <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}" @if ($required) required @endif
              @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
              {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $error]) }}>{{ old($name, $value) }}</textarea>
    @if ($error)
        <div class="invalid-feedback" id="{{ $id }}-error">{{ $error }}</div>
    @elseif ($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>
