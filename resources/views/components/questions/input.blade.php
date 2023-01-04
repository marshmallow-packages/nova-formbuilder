@props(['required' => null, 'type' => 'text', 'value' => null, 'autofocus' => null, 'width' => 'full', 'hideLabel' => false, 'readonly' => null, 'disabled' => false, 'question' => null])

@php

    $placeholder = $attributes['placeholder'];

    $defer = false;
    $lazy = false;
    if ($attributes->has('defer')) {
        $defer = $attributes['defer'] == 'true' || $attributes['defer'] == true;
    }

    if ($attributes->has('lazy')) {
        $lazy = $attributes['lazy'] == 'true' || $attributes['lazy'] == true;
    }

    if (!$attributes['type']) {
        $attributes['type'] = $type;
    }

    if ($type == 'input') {
        $type = 'text';
    }

    $required = $required == 1 || $required == true ? true : false;

    if (!$required) {
        if (!$hideLabel) {
            $placeholder .= ' (optioneel)';
        }
        $required = false;
    }
    $required = false;

    $class = $attributes->get('class') ?? 'w-full';

    $step = null;
    $min = null;
    $max = null;
    $onInput = null;
    if ($question) {
        if ($question->type == 'number' || $question->type == 'range') {
            $min = $question->digit_min;
            $max = $question->digit_max;
        }

        if ($question->type == 'range') {
            $step = $question->digit_step ?? 1;
        }

        if ($question->prefix) {
            $prefix = $question->prefix;
            $class .= ' w-full pl-8';
        }
        if ($question->suffix) {
            $suffix = $question->suffix;
            $class .= ' w-full pr-10';
        }

        if ($question->type == 'color') {
            $class .= ' py-2 px-4';
        }
        if ($question->has_dependents) {
            $defer = false;
            $lazy = true;
        }

        if ($question->type == 'range') {
            $onInput = $attributes['id'] . '_rangeValue.innerText = this.value';
        }
    }

@endphp


<div class="relative w-full">
    @if (!$hideLabel)
        <x-mm-form-labels.tooltip-label :required="$required" for="{{ $attributes['id'] }}"
            placeholder="{{ $attributes['label'] ?? $attributes['placeholder'] }}" :question="$question" />
    @endif

    <div class="relative w-full">
        @isset($prefix)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <span class="sm:text-sm">{!! $prefix !!}</span>
            </div>
        @endisset
        {{-- pl-7 pr-12 --}}
        @if ($defer)
            <x-mm-form-question.default-input :required="$required" :id="$attributes['id']" :x-ref="$attributes['x-ref']" :type="$attributes['type']"
                :class="$class" :name="$attributes['name']" :value="$value" :autofocus="$autofocus" :type="$type"
                :oninput="$onInput" :step="$step" :min="$min" :max="$max"
                wire:model.defer="{{ $attributes['name'] }}" :placeholder="$placeholder" :autocomplete="$attributes['autocomplete']" :readonly="$readonly"
                :disabled="$disabled" />
        @elseif($lazy)
            <x-mm-form-question.default-input :required="$required" :id="$attributes['id']" :x-ref="$attributes['x-ref']"
                :class="$class" :step="$step" :min="$min" :max="$max" :type="$attributes['type']"
                :name="$attributes['name']" :value="$value" :autofocus="$autofocus" :type="$type"
                wire:model.lazy="{{ $attributes['name'] }}" :placeholder="$placeholder" :autocomplete="$attributes['autocomplete']" :readonly="$readonly"
                :disabled="$disabled" />
        @else
            <x-mm-form-question.default-input :required="$required" :id="$attributes['id']" :type="$attributes['type']"
                :class="$class" :step="$step" :min="$min" :max="$max" :name="$attributes['name']"
                :value="$value" :autofocus="$autofocus" :type="$type" wire:model="{{ $attributes['name'] }}"
                :placeholder="$placeholder" :autocomplete="$attributes['autocomplete']" :readonly="$readonly" :disabled="$disabled" />
        @endif

        @isset($suffix)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <span class="text-gray-500 sm:text-sm" id="price-currency">{!! $suffix !!}</span>
            </div>
        @endisset

        @error($attributes['id'])
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <i class="text-red-500 fa-duotone fa-circle-exclamation"></i>
            </div>
        @enderror

        @if ($type == 'range')
            <div class="flex justify-between w-full text-sm text-gray-400">
                <span>{{ $min }}</span>
                <span class="font-sans text-sm font-semibold text-gray-500"
                    id="{{ $attributes['id'] }}_rangeValue">0</span>
                <span>{{ $max }}</span>
            </div>
        @endif
    </div>
    {{-- <x-mm-form-error for="{{ $attributes['id'] }}" :icon="false" /> --}}
</div>
