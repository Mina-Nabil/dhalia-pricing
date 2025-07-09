<div>
    <div class="card">
        <header class=" card-header noborder">
            <h4 class="card-title">
                Activity Log
            </h4>
            <input type="text" class="form-control w-auto d-inline-block cursor-pointer" style="width:auto" name="datetimes" id="reportrange" />
        </header>
        <div class="card-body px-6 pb-6">
            <div class=" -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>

                                    <th scope="col" class=" table-th ">
                                        User
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Level
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Title
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Description
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Time
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach ($logs as $log)
                                    <tr wire:click="showLogInfo({{ $log->id }})" class="table-td hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">
                                        <td class="table-td ">{{ $log->user?->username }}</td>

                                        <td class="table-td">
                                            @if ($log->level === 'info')
                                                <span class="badge bg-info-500 text-info-500 bg-opacity-30 capitalize">{{ $log->level }}</span>
                                            @elseif($log->level === 'error')
                                                <span class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize">{{ $log->level }}</span>
                                            @elseif($log->level === 'warning')
                                                <span class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize">{{ $log->level }}</span>
                                            @else
                                                <span class="badge bg-secondary-500 text-secondary-500 bg-opacity-30 capitalize">{{ $log->level }}</span>
                                            @endif
                                        </td>

                                        <td class="table-td ">{{ $log->title }}</td>

                                        <td class="table-td @if ($log->desc != '') !toolTip onTop @endif" data-tippy-content="{{ $log->desc }}" data-tippy-theme="dark">
                                            {{ strlen($log->desc) > 50 ? substr($log->desc, 0, 50) . '...' : $log->desc }}
                                        </td>

                                        <td class="table-td ">{{ $log->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($logs->isEmpty())
                            {{-- START: empty filter result --}}
                            <div class="card m-5 p-5">
                                <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                    <div class="items-center text-center p-5">
                                        <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon></h2>
                                        <h2 class="card-title text-slate-900 dark:text-white mb-3">No Logs with the
                                            applied
                                            filters</h2>
                                        <p class="card-text">Try changing the filters or search terms for this view.
                                        </p>
                                        <a href="{{ url('/logs') }}" class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                            all Logs</a>
                                    </div>
                                </div>
                            </div>
                            {{-- END: empty filter result --}}
                        @endif
                    </div>
                    {{ $logs->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    @if ($LogId)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 800px;">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Log Info
                            </h3>

                            <button wire:click="closeLogInfo" type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                        11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div>
                                @if ($level === 'info')
                                    <span class="badge bg-info-500 text-info-500 bg-opacity-30 capitalize">{{ $level }}</span>
                                @elseif($level === 'error')
                                    <span class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize">{{ $level }}</span>
                                @elseif($level === 'warning')
                                    <span class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize">{{ $level }}</span>
                                @else
                                    <span class="badge bg-secondary-500 text-secondary-500 bg-opacity-30 capitalize">{{ $level }}</span>
                                @endif
                            </div>
                            <div class="text-sm">
                                {{ $user }}
                            </div>
                            <h6>
                                {{ $title }}
                            </h6>
                            <div class="text-sm text-slate-600 dark:text-slate-300">
                                {{ $desc }}
                            </div>
                            <div class="text-sm">
                                <iconify-icon icon="mingcute:time-line"></iconify-icon> {{ $time }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            var start = moment('{{ $fromDate }}');
            var end = moment('{{ $toDate }}');

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                Livewire.dispatch('dateRangeSelected', {
                    data: [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
                });

            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'months').startOf('month'), moment().subtract(1,
                        'months').endOf('month')],
                    'Last 3 Months': [moment().subtract(3, 'months'), moment()],
                }
            }, cb);

            cb(start, end);
        });
    </script>
</div>
