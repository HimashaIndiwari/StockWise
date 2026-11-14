{{-- Bootstrap select. $options: [value => label]. --}}
@props(['name', 'label', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null, 'help' => null])
@php
    $id = $attributes->get('id', 'field-'.$name);
    $error = $errors->first($name);
    $current = (string) old($name, $value);
@endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if ($required)<span class="text-danger" aria-hidden="true"> *</span>@endif
    </label>
    <select name="{{ $name }}" id="{{ $id }}" @if ($required) required @endif
            @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->except('id')->class(['form-select', 'is-invalid' => $error]) }}>
        @if (! is_null($placeholder))
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($error)
        <div class="invalid-feedback" id="{{ $id }}-error">{{ $error }}</div>
    @elseif ($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>
