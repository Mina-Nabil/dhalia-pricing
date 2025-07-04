@props(['disabled' => false, 'loadingFunction' => null])

<button {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' =>
            'btn inline-flex justify-center items-center gap-2 text-white bg-black-500' . ($disabled ? ' opacity-25' : ''),
    ]) }}>

    {{-- Spinner (only shows if wireClickMethod exists) --}}
    @if ($loadingFunction)
        <span wire:loading.remove wire:target="{{ $loadingFunction }}">
            {{ $slot }}
        </span>

        <iconify-icon wire:loading wire:target="{{ $loadingFunction }}" icon="line-md:loading-twotone-loop"
            class="text-xl spin-slow">
        </iconify-icon>
    @else
        {{-- If no wire:click is passed, show slot only --}}
        {{ $slot }}
    @endif

</button>
