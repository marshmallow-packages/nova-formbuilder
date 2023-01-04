<div @class([
    'px-2 md:px-6 pt-4 col-span-12' => $fullWidth,
    'px-8 pb-6 pt-4  ' => !$fullWidth,
    'relative pb-4 md:relative md:col-span-12 border-b border-gray-100 ' => $stopLink,
])>

    <div class="flex flex-row text-gray-600" :class="{ 'justify-end': firstStep == lastStep }">
        <div x-show="firstStep !== lastStep"
            class="self-center flex-1 font-sans text-sm font-medium text-gray-600 justify-self-center">
            @if (!isset($hideSteps) || $hideSteps == false)
                <div x-show="currentStep >= 1 && !isSubmitted" x-cloak>
                    {{ __('Stap') }}
                    <span class="font-semibold" x-text="currentStep"></span>
                    {{ __('van') }}
                    <span class="font-semibold" x-text="lastStep"></span>
                </div>
            @endif
        </div>

        @if ($stopLink)
            <div class="self-center flex-0 justify-self-end">
                @if ($stopLink == 'modal')
                    <button type="button" wire:click="$emit('closeModal')"
                        class="relative w-auto h-auto font-sans text-sm font-medium text-gray-600 group hover:text-primary-600 focus:outline-none"
                        aria-label="close">
                        <i class="mr-1 fal fa-times"></i>
                        {{ __('Close') }}
                    </button>
                @else
                    <a href="{{ $stopLink ?? '/' }}"
                        class="relative w-auto h-auto font-sans text-sm font-medium text-gray-600 group hover:text-primary-600 focus:outline-none"
                        aria-label="stop">
                        <i class="mr-1 fal fa-times"></i>
                        {{ __('Stoppen') }}
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>
