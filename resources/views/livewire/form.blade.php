<div class="flex-1 w-full ">

    <div class="w-full" x-data="{
        currentStep: @entangle('currentStep'),
        lastStep: @entangle('lastStep'),
        firstStep: @entangle('firstStep'),
        isSubmitted: @entangle('submitted'),
    }" x-cloak>

        @if (!$submitted)
            <x-mm-forms-sections.header :stopLink="$form['on_cancel']" :fullWidth="$full_width" />
        @endif

        <x-mm-forms-sections.title :fullWidth="$full_width" sidebar="false">
            <x-slot name="title">
                {{ $formStepsArray[$currentStep]['title'] }}
            </x-slot>
            <x-slot name="subtitle">
                {{ $formStepsArray[$currentStep]['subtitle'] }}
            </x-slot>
            <x-slot name="description">
                {{ $formStepsArray[$currentStep]['description'] }}
            </x-slot>
        </x-mm-forms-sections.title>

        @if ($submitted)
            <div class="relative col-span-12 px-2 pt-4 pb-8 md:px-6 ">
                <h2 class="text-xl font-semibold text-primary-500 ">
                    {{ $end_page_layout['thanks_title'] ?? '' }}
                </h2>
                <p class="text-base text-gray-700 ">
                    {{ $end_page_layout['thanks_text'] ?? '' }}
                </p>
            </div>
        @else
            @foreach ($formSteps as $step)
                @if ($currentStep == $step['step_number'])
                    <livewire:mm-forms-step :step="$step['step_number']" :questions="$step->questions" :form_id="$form_id" :form_submission_data="$form_submission_data"
                        :wire:key="'forms-step-'.$step" />
                @endif
            @endforeach
        @endif
    </div>
</div>
