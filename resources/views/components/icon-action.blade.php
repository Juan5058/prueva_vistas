@props([
    'tooltip',
    'href' => null,
    'navigate' => true,
    'variant' => 'neutral',
])

@php
    $colors = [
        'neutral' => 'text-slate-600 hover:bg-slate-100',
        'info' => 'text-blue-600 hover:bg-blue-50',
        'danger' => 'text-rose-600 hover:bg-rose-50',
        'warning' => 'text-amber-700 hover:bg-amber-50',
    ];
    $class = 'relative inline-flex items-center justify-center p-1.5 rounded-lg '.$colors[$variant];
@endphp

@if($href)
    <a href="{{ $href }}" @if($navigate) wire:navigate @endif title="{{ $tooltip }}" {{ $attributes->merge(['class' => $class]) }} aria-label="{{ $tooltip }}">
        {{ $slot }}
    </a>
@else
    <button type="button" title="{{ $tooltip }}" {{ $attributes->merge(['class' => $class]) }} aria-label="{{ $tooltip }}">
        {{ $slot }}
    </button>
@endif
