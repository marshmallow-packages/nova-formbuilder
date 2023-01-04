@props(['disabled' => false, 'required' => null, 'question' => null])

@php

    $defer = false;
    $lazy = false;
    if ($attributes->has('defer')) {
        $defer = $attributes['defer'] == 'true' || $attributes['defer'] == true;
    }

    if ($attributes->has('lazy')) {
        $lazy = $attributes['lazy'] == 'true' || $attributes['lazy'] == true;
    }

    $label = null;
    $placeholder = null;

    if (isset($question)) {
        $label = $question->label;
        $placeholder = $question->placeholder;
    }

    if ($label && $attributes->has('label')) {
        $label = $attributes->get('label');
    }

    if ($attributes->has('placeholder')) {
        $placeholder = $attributes->get('placeholder');
    }

    if ($required) {
        $placeholder .= '*';
    }

    $class = 'border-gray-300 focus:border-primary-300 focus:ring  text-[0.9rem] focus:ring-primary-200 focus:ring-opacity-50 rounded-md shadow-sm w-full min-h-10 placeholder-white-600 bg-transparent border p-3  placeholder-text-md peer focus:outline-none  read-only:italic read-only:bg-gray-300/20 disabled:italic disabled:bg-gray-300/20';

@endphp

@error($attributes['id'])
    @php
        $class = 'border-red-500/40 ' . $class;
    @endphp
@enderror

<div class="relative" x-data="{ active: true, count: 0, maxCount: 0 }" x-init="count = $refs.countme.value.length;
maxCount = $refs.countme.maxLength">

    <x-mm-forms-labels.tooltip-label class="mb-1 ml-1" :required="$required" for="{{ $attributes['id'] }}" :placeholder="$attributes['placeholder']"
        :question="$question" />

    <textarea rows="4" maxLength="500" x-ref="countme" id="{{ $attributes['id'] }}" name="{{ $attributes['name'] }}"
        wire:loading.attr="disabled"
        @if ($defer) wire:model.defer="{{ $attributes['name'] }}" @elseif ($lazy) wire:model.lazy="{{ $attributes['name'] }}" @else wire:model="{{ $attributes['name'] }}" @endif
        placeholder="{{ $placeholder }}" x-on:keyup="count = $refs.countme.value.length"
        @if ($disabled) disabled @endif class="{{ $class }}">{{ $slot }}</textarea>


    <div class="mt-1 text-sm text-gray-400 text-opacity-70" x-cloak x-show="active && maxCount > 0">
        <span x-html="count"></span> / <span x-html="maxCount"> </span> {{ __('karakters') }}
    </div>

    <x-mm-forms-error for="{{ $attributes['id'] }}" />

</div>
