@props(['question' => null, 'required' => false, 'has_info' => false])

@php
    $placeholder = $attributes['placeholder'] ?? null;
    $for = $attributes['for'] ?? null;

    $has_info = false;
    $question_info = null;

    if ($question) {
        $placeholder = $question->label ?: $question->placeholder;
        $for = $for ?: $question->id;
        $required = $question->required;
        if ($question->info_tooltip) {
            $has_info = $question->info;
            $question_info = $question->getInfo();
        }
    }

@endphp

<div class="inline-flex items-center space-x-2">
    <x-mm-forms-labels.label :class="$attributes['class']" :required="$required" for="{{ $for }}">
        {{ $placeholder }}
    </x-mm-forms-labels.label>
    @if ($has_info)
        <x-mm-forms-labels.tooltip content="{!! $question_info !!}" class="z-10">
            <div class="mb-0.5 text-sm cursor-pointer  hover:text-primary-600 text-primary-500">
                <i class="fa-duotone fa-info-circle "></i>
            </div>
        </x-mm-forms-labels.tooltip>
    @endif
</div>
