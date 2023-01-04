@props(['required' => null, 'type' => 'text', 'value' => null, 'autofocus' => null, 'width' => 'full', 'hideLabel' => false, 'options' => [], 'question' => null, 'display_prices' => false])

@php
    $defer = false;
    if ($attributes->has('defer')) {
        $defer = $attributes['defer'] == 'true' || $attributes['defer'] == true;
    }

    $placeholder = $attributes['placeholder'];

    if (!$required) {
        if (!$hideLabel) {
            $placeholder .= ' (optioneel)';
        }
        $required = false;
    }

    $class = 'appearance-none border-gray-400/40 placeholder:text-sm text-gray-700 focus:border-primary-300 disabled:italic disabled:bg-gray-300/20 bg-white/50 relative py-2.5 focus:ring focus:ring-primary-200 focus:ring-opacity-50 rounded-md text-[0.9rem] w-full';

    $select_options = [];
    foreach ($options as $value => $option) {
        if (is_array($option)) {
            $value = $option['id'];
            $label = $option['title'];
            if ($display_prices) {
                $option_price = Arr::get($option, 'price', 0);
                if ($option_price != 0) {
                    if ($option_price > 0) {
                        $label .= ' (+ ' . f($option_price) . ')';
                    } else {
                        $label .= ' (- ' . f(abs($option_price)) . ')';
                    }
                }
            }
            $select_options[$value] = $label;
        } else {
            $select_options[$value] = $option;
        }
    }

@endphp

@error($attributes['id'])
    @php
        $class = 'border-red-500/40 ' . $class;
    @endphp
@enderror


<div class="relative w-full">
    @if (!$hideLabel)
        <x-mm-forms-labels.tooltip-label :required="$required" for="{{ $attributes['id'] }}" :placeholder="$attributes['placeholder']"
            :question="$question" />
    @endif
    @if ($defer)
        <select id="{{ $attributes['id'] }}" wire:model.defer="{{ $attributes['name'] }}"
            @if ($attributes['x-on:change']) x-on:change="{{ $attributes['x-on:change'] }}" @endif
            class="{{ $class }}">
            <option value="" hidden selected>
                {{ __('Maak een keuze') }}
            </option>
            @foreach ($select_options as $value => $option)
                <option value="{{ $value }}">
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @else
        <select id="{{ $attributes['id'] }}" wire:model="{{ $attributes['name'] }}"
            @if ($attributes['x-on:change']) x-on:change="{{ $attributes['x-on:change'] }}" @endif
            class="{{ $class }}">
            <option value="" hidden selected>
                {{ __('Maak een keuze') }}
            </option>
            @foreach ($select_options as $value => $option)
                <option value="{{ $value }}">
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @endif
    <x-mm-forms-error for="{{ $attributes['id'] }}" />
</div>
