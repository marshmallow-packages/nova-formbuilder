@props(['for', 'icon' => true])

@error($for)
    <div {{ $attributes->merge(['class' => 'mt-1 text-sm']) }}>
        <span {{ $attributes->merge(['class' => ' text-sm text-red-500']) }}>
            @if ($icon)
                <i class="mr-1 fa-duotone fa-circle-exclamation"></i>
            @endif
            {{ $message }}
        </span>
    </div>
@enderror
