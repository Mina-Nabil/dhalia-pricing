@props(['id' => null, 'maxWidth' => '2xl'])

@php
    $wireModel = $attributes->wire('model');
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
    ][$maxWidth];
@endphp

<div x-data="{ showModal: @entangle($wireModel) }" x-on:close.stop="showModal = false" x-on:keydown.escape.window="showModal = false"
    x-bind:class="showModal ?
        'modal fade fixed top-0 left-0 w-full h-full outline-none overflow-y-auto show flex items-start justify-center' :
        'hidden'"
    style="padding: 1.5rem 0;" x-show="showModal"
    x-effect="if(showModal){
        $el.classList.remove('hidden');
        $el.classList.add('show');
    }else{
        $el.classList.add('hidden');
        $el.classList.remove('show');
    }">
    <div x-show="showModal" class="fixed inset-0 transition-all transform" x-on:click="showModal = false"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900"></div>
    </div>

    <div x-show="showModal"
        class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all w-[75%] sm:w-full {{ $maxWidth }} sm:mx-auto my-4"
        x-trap.inert.noscroll="showModal" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
            <div class="text-lg font-medium text-white">
                {{ $title }}
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                {{ $slot }}
            </div>
        </div>

        @if (isset($footer))
            <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 text-right">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
