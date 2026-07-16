@props(['active' => false, 'href' => '#'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-mono text-sm font-medium transition-all duration-300 rounded-full px-6 py-2 whitespace-nowrap focus:outline-none';
    $stateClasses = $active 
        ? 'bg-cocoa text-white shadow-soft' 
        : 'bg-white text-cocoa/70 hover:bg-dough hover:text-cocoa border border-dough/50';
    $mergedClasses = $baseClasses . ' ' . $stateClasses;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $mergedClasses]) }}>
    {{ $slot }}
</a>
