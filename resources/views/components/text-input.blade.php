@props(['disabled' => false, 'errorMessage' => null, 'label' => null, 'listOptions' => null])

@php
    $options = [];
    if ($listOptions) {
        $options = explode(",", $listOptions);
    }
@endphp

<div class="from-group">
    <div class="input-area">
        <x-input-label>{{ $label }}</x-input-label>
        <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
            'class' =>
                'form-control border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm w-full',
        ]) !!}
            @if (count($options)) list="{{ $label }}" @endif>
        @if (count($options))
            <datalist id="{{ $label }}">
                @foreach ($options as $item)
                    <option value="{{ $item }}"></option>
                @endforeach
            </datalist>
        @endif

        @if ($errorMessage)
            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $errorMessage }}</span>
        @endif

    </div>
</div>
