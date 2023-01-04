@props(['content' => null, 'class' => 'inline mx-1 text-sm text-gray-600 hover:text-primary-500', 'id' => null])

@php
    $id = $id ?? md5($content);
@endphp

<span id="tooltip_for_{{ $id }}" class="{{ $class }}" x-data x-tooltip="{{ $content }}">
    @if ((string) $slot)
        {{ $slot }}
    @else
        <i class="fa-duotone fa-circle-info"></i>
    @endif
</span>
