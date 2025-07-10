<div class="card">

    <header class="card-header bg-slate-800 dark:bg-slate-900">
        <h4 class="text-white card-title  font-semibold">{{ $title }}</h4>
        @if (isset($tools))
            <div class="card-tools">
                {{ $tools }}
            </div>
        @endif
    </header>

    <div class="card-body p-3">
        {{ $slot }}
    </div>


</div>
