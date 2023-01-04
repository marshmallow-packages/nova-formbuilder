@props(['required' => null, 'value'])

@isset($errors)
    @error($attributes['for'])
        @php
            $class = 'font-medium text-red-600 ';
        @endphp
    @else
        @php
            $class = 'font-medium text-gray-700 ';
        @endphp
    @enderror
@endisset

<label class="{{ $class }} block w-full mb-1 ml-px text-sm ">
    {{ $value ?? $slot }}
    @if ($required)
        <span class="font-semibold text-red-500">*</span>
    @endif
</label>
