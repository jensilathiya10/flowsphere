@props(['name', 'label', 'type' => 'text'])

@php
$hasError = $errors->has($name);
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name) }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 '
                . ($hasError ? 'border-red-400' : 'border-gray-300'),
        ]) }}
    >

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
