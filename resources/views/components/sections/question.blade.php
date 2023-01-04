@props(['has_autocomplete', 'content', 'fullWidth', 'sidebar'])
<form x-cloak wire:submit.prevent="submitStep" @class([
    'flex flex-col justify-between h-full' => $sidebar,
    'grid-cols-12 flex-1 col-span-12 md:grid' => !$sidebar,
    'relative',
]) x-data="{
    buttonLoading: false,
}"
    autocomplete="{{ $has_autocomplete ? 'on' : 'off' }}">
    <x-honeypot livewire-model="extraFields" />
    <div @class([
        'md:px-4 col-span-12' => $fullWidth,
        'md:px-8 pb-4 md:col-start-2 md:col-span-10' => !$fullWidth,
        'bg-white',
    ])>
        <div class="mt-2 md:mt-0 md:col-span-3 " x-init="$nextTick(() => {
            let startDiv = document.getElementById('start-question');
            if (startDiv && currentStep > 1) {
                startDiv.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
            }
        })">
            <div @class([
                'px-2 mt-2' => $fullWidth,
                'md:px-4' => !$fullWidth,
                'pb-4 w-full',
            ]) id="start-question">
                @if ($content->isNotEmpty())
                    <div class="grid grid-cols-6 gap-6 ">
                        {{ $content }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div @class([
        'px-4 col-span-12' => $fullWidth,
        'px-12 ' => !$fullWidth,
        ' flex-1' => !$sidebar,
        'py-4 mt-4 border-t border-gray-100 md:col-span-12 md:relative',
    ])>

        <div class="flex">
            <div class="self-center flex-1 justify-self-center" x-data="{ shouldShow: currentStep > 1 }" x-init="$watch('currentStep', value => (shouldShow = currentStep > 1))">
                <button type="button" x-show="shouldShow" x-cloak x-on:click="$wire.emit('MMForms:Form:previousStep')"
                    class="relative w-auto h-auto text-base text-gray-500 group hover:text-primary-500 focus:outline-none"
                    aria-label="previous">
                    <i class="mr-1 fal fa-chevron-left"></i>
                    {{ __('Vorige') }}
                </button>
            </div>

            <div class="self-center flex-0 justify-self-end" x-data="{ shouldShow: currentStep == lastStep }" x-init="$watch('currentStep', value => (shouldShow = currentStep == lastStep));">

                <div class="flex " x-cloak x-show="!shouldShow">
                    <x-mm-forms-action-message on="stepSaved" class="self-center mr-3">
                        {{ __('Opgeslagen') }}
                    </x-mm-forms-action-message>

                    <x-mm-forms-buttons.button aria-label="next" wire:loading.attr="disabled">
                        {{ __('Volgende') }}
                        <i class="ml-1 fal fa-chevron-right"></i>
                    </x-mm-forms-buttons.button>
                </div>

                <x-mm-forms-buttons.button x-cloak aria-label="next" x-show="shouldShow" wire:loading.attr="disabled">
                    {{ __('Versturen') }}
                </x-mm-forms-buttons.button>
            </div>

        </div>
    </div>

</form>
