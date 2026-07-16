@props(['as' => 'button', 'href' => '#', 'type' => 'button', 'class' => ''])

@php
    $baseClasses = 'inline-flex items-center justify-center font-mono text-sm font-semibold tracking-wide transition-all duration-300 rounded-full px-8 py-4 focus:outline-none focus:ring-2 focus:ring-caramel focus:ring-offset-2 focus:ring-offset-cream active:scale-95 shadow-soft hover:shadow-float hover:-translate-y-1';
    $primaryClasses = 'bg-caramel text-white hover:bg-cocoa';
    $mergedClasses = $baseClasses . ' ' . $primaryClasses . ' ' . $class;
@endphp

@if($as === 'button')
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $mergedClasses]) }}>
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $mergedClasses]) }}>
        {{ $slot }}
    </a>
@endif
