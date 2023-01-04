@props(['disabled' => false])

@php
    $extra_classes = $attributes->get('class') ?? null;
    $class = $extra_classes . ' border outline-none border-gray-400/40 placeholder:text-[0.9rem] text-gray-700 focus:border-primary-300 disabled:italic disabled:bg-gray-300/20 bg-white/50 relative py-2.5 focus:ring focus:ring-primary-200 focus:ring-opacity-50 rounded-md  text-[0.9rem] read-only:italic read-only:bg-gray-300/20 disabled:italic disabled:bg-gray-300/20 ';

    $show_view_toggle = $attributes->has('viewToggle') && $attributes->get('viewToggle');
@endphp

@isset($errors)
    @error($attributes['id'])
        @php
            $class = 'border-red-500/40 ' . $class;
        @endphp
    @enderror
@endisset

<span x-data="{ showValue: false }" class="relative">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' => $class,
    ]) !!}
        @if ($show_view_toggle) :type="showValue ? 'text' : 'password'" @endif>

    @if ($show_view_toggle)
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-sm leading-5">
            <span class="cursor-pointer" @click="showValue = !showValue">
                <i class="fa-duotone fa-eye-slash" :class="showValue ? 'hidden' : ''"></i>
                <i class="fa-duotone fa-eye" :class="showValue ? '' : 'hidden'"></i>
            </span>
        </div>
    @endif
</span>
