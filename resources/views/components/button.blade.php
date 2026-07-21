@props(['variant' => 'primary', 'type' => 'button'])

@php
$variants = [
    'primary' => 'bg-brand-600 text-white hover:bg-brand-700 focus-visible:outline-brand-600',
    'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus-visible:outline-gray-400',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed ' . ($variants[$variant] ?? $variants['primary'])]) }}
>
    {{ $slot }}
</button>
