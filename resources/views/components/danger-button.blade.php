@props(['disabled' => false])

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-danger-700 border border-danger-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-danger-300 uppercase tracking-widest shadow-sm hover:bg-danger-50 dark:hover:bg-danger-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-danger-800 disabled:opacity-25 transition ease-in-out duration-150' . ($disabled ? ' opacity-25' : '')]) }}>
    {{ $slot }}
</button> 