<x-mm-forms-sections.question :sidebar="$sidebar ?? false" :fullWidth="$full_width" :stepNumber="$stepNumber"
    has_autocomplete="{{ $has_autocomplete ?? false }}">
    <x-slot name="content">
        <div class="grid-cols-12 col-span-6 gap-6 space-y-6 md:space-y-3 md:gap-4 md:grid">
            @foreach ($questions as $key => $question)
                @php
                    $width = match ($question->width) {
                        '1/2' => 'sm:col-span-6',
                        '1/4' => 'sm:col-span-3',
                        '3/4' => 'sm:col-span-9',
                        '2/3' => 'sm:col-span-8',
                        '1/3' => 'sm:col-span-4',
                        default => 'sm:col-span-12',
                    };
                @endphp

                @if (!$question->display)
                    <div class="col-span-6 md:space-y-2 space-y-2  {{ $width }}">
                    </div>
                    @continue
                @endif

                <div class="col-span-6 md:space-y-2 space-y-2 mt-3 {{ $width }}">

                    @php
                        $options = $question->getOptionsArray();
                    @endphp

                    @if ($question->isInputField())
                        <x-mm-forms-questions.input id="{{ $question->name }}" :defer="$question->defer ?? true" :required="$question->required"
                            name="state.{{ $question->name }}" :type="$question->type" label="{{ $question->getLabel() }}"
                            :question="$question" prefix="{!! $question->prefix !!}" placeholder="{{ $question->placeholder }}"
                            autocomplete="{{ $question->autocomplete ?? 'off' }}" />
                    @else
                        @switch($question->type)
                            @case('hidden')
                                <x-mm-forms-questions.input id="{{ $question->name }}" :defer="$question->defer ?? true" :required="$question->required"
                                    name="state.{{ $question->name }}" :type="$question->type" label="{{ $question->getLabel() }}"
                                    :question="$question" prefix="{!! $question->prefix !!}" :autocomplete="$question->autocomplete"
                                    placeholder="{{ $question->placeholder }}" />
                            @break

                            @case('textarea')
                                <x-mm-forms-questions.textarea id="{{ $question->name }}" :defer="$question->defer ?? true" :required="$question->required"
                                    :question="$question" name="state.{{ $question->name }}" type="{{ $question->type }}"
                                    label="{{ $question->getLabel() }}" prefix="{!! $question->prefix !!}"
                                    placeholder="{{ $question->placeholder }}" />
                            @break

                            @case('checkbox')
                                <x-mm-forms-questions.checkbox id="{{ $question->name }}" :required="$question->required" :options="$options"
                                    :question="$question" name="state.{{ $question->name }}"
                                    placeholder="{{ $question->placeholder }}" :state="$state" />
                            @break

                            @case('radio')
                                <x-mm-forms-questions.radio id="{{ $question->name }}" :required="$question->required" :options="$options"
                                    :question="$question" name="state.{{ $question->name }}"
                                    placeholder="{{ $question->placeholder }}" :state="$state" />
                            @break

                            @case('select')
                                <x-mm-forms-questions.select id="{{ $question->name }}" :required="$question->required" :options="$options"
                                    :question="$question" name="state.{{ $question->name }}"
                                    placeholder="{{ $question->placeholder }}" />
                            @break

                            @case('photo')
                                @if (config('nova-formbuilder.spatie_media_library_pro'))
                                    <div class="relative w-full ">
                                        <x-mm-forms-labels.tooltip-label for="{{ $question->media_collection_name }}"
                                            :question="$question" />

                                        @if ($form_submission_model)
                                            <x-media-library-collection max-items="1"
                                                name="{{ $question->media_collection_name }}" :model="$form_submission_model"
                                                rules="mimes:jpeg,bmp,png,pdf"
                                                collection="{{ $question->media_collection_name }}" />
                                        @else
                                            <x-media-library-attachment max-items="1"
                                                name="{{ $question->media_collection_name }}" rules="mimes:jpeg,bmp,png,pdf"
                                                collection="{{ $question->media_collection_name }}" />
                                        @endif
                                    </div>
                                @endif
                            @break

                            @case('photos')
                                @if (config('nova-formbuilder.spatie_media_library_pro'))
                                    <div class="relative w-full">
                                        <x-mm-forms-labels.tooltip-label for="{{ $question->media_collection_name }}"
                                            :question="$question" />
                                        @if ($form_submission_model)
                                            <x-media-library-collection name="{{ $question->media_collection_name }}"
                                                :model="$form_submission_model" rules="mimes:jpeg,bmp,png,pdf"
                                                collection="{{ $question->media_collection_name }}" />
                                        @else
                                            <x-media-library-attachment name="{{ $question->media_collection_name }}"
                                                rules="mimes:jpeg,bmp,png,pdf"
                                                collection="{{ $question->media_collection_name }}" />
                                        @endif
                                    </div>
                                @endif
                            @break

                            @default
                                @if (!$question->isInputField())
                                    @ray($question->type . ' - not found')
                                @endif
                        @endswitch
                    @endif

                    @if ($question->info && !$question->info_tooltip)
                        <div class="font-sans w-full !text-sm  text-gray-500 items-center flex">
                            <div class="mr-2">
                                <i class=" fa-duotone fa-info-circle text-primary-500"></i>
                            </div>
                            <div
                                class="font-sans mb-0.5  w-full !text-sm prose-p:text-sm prose-p:font-sans prose-a:font-sans prose prose-a:text-gray-500  prose-a:underline prose-a:hover:text-primary-500 text-gray-500 ">
                                {!! $question->getInfo() !!}
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </x-slot>
</x-mm-forms-sections.question>
