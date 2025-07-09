<!DOCTYPE html>
<html lang="zxx" dir="ltr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>Pricing</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/fav-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- BEGIN: Theme CSS-->
    {{--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    --}}
    <link rel="stylesheet" href="{{ asset('css/rt-plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('headcontent')
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Alpine.js CDN with Focus plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    <!-- End : Theme CSS-->
    <script src="{{ asset('js/settings.js') }}" sync></script>
    @auth
        {{-- Sweet Alert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endauth

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @yield('child_styles')

</head>

<body class=" font-inter dashcode-app">
    <!-- [if IE]> <p class="browserupgrade"> You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security. </p> <![endif] -->
    <main class="app-wrapper">
        <!-- BEGIN: Sidebar -->
        <div class="sidebar-wrapper group">
            <div id="bodyOverlay"
                class="w-screen h-screen fixed top-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm z-10 hidden"></div>
            <div class="logo-segment">
                <a class="flex items-center p-2" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo/pricing-logo.png') }}" class="black_logo" alt="logo">
                    <img src="{{ asset('images/logo/pricing-logo.png') }}" class="white_logo" alt="logo">
                    {{-- <span
                        class="ltr:ml-3 rtl:mr-3 text-xl font-Inter font-bold text-slate-900 dark:text-white">Wise
                        Ins.</span> --}}
                </a>
                <!-- Sidebar Type Button -->
                <div id="sidebar_type" class="cursor-pointer text-slate-900 dark:text-white text-lg">
                    <span class="sidebarDotIcon extend-icon cursor-pointer text-slate-900 dark:text-white text-2xl">
                        <div
                            class="h-4 w-4 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-full transition-all duration-150 ring-2 ring-inset ring-offset-4 ring-black-900 dark:ring-slate-400 bg-slate-900 dark:bg-slate-400 dark:ring-offset-slate-700">
                        </div>
                    </span>
                    <span class="sidebarDotIcon collapsed-icon cursor-pointer text-slate-900 dark:text-white text-2xl">
                        <div
                            class="h-4 w-4 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-full transition-all duration-150">
                        </div>
                    </span>
                </div>
                <button class="sidebarCloseIcon text-2xl">
                    <iconify-icon class="text-slate-900 dark:text-slate-200" icon="clarity:window-close-line">
                    </iconify-icon>
                </button>
            </div>
            <div id="nav_shadow"
                class="nav_shadow h-[60px] absolute top-[80px] nav-shadow z-[1] w-full transition-all duration-200 pointer-events-none
      opacity-0">
            </div>
            <div class="sidebar-menus bg-white dark:bg-slate-800 py-2 px-4 h-[calc(100%-80px)] overflow-y-auto z-50"
                id="sidebar_menus">
                <ul class="sidebar-menu">
                    <li>
                        <a class="navItem {{ request()->routeIs('offers.index') ? 'active' : '' }}"
                            href="{{ url('/offers') }}">Offers</a>
                    </li>
                    <li>
                        <a class="navItem {{ request()->routeIs('clients.index') ? 'active' : '' }}"
                            href="{{ url('/clients') }}">Clients</a>
                    </li>


                    @can('view-product-list')
                        <li>
                            <a class="navItem {{ request()->routeIs('products.index') ? 'active' : '' }}"
                                href="{{ url('/settings/products') }}">Products</a>
                        </li>
                    @endcan

                    <li class="">
                        <a href="javascript:void(0)" class="navItem">
                            <span class="flex items-center">
                                <span>Settings</span>
                            </span>
                            <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                        </a>
                        <ul class="sidebar-submenu">
                            @can('view-user-list')
                                <li>
                                    <a class="{{ request()->routeIs('users.index') ? 'active' : '' }}"
                                        href="{{ url('/settings/users') }}">Users</a>
                                </li>
                            @endcan
                            @can('view-packing-list')
                                <li>
                                    <a class="{{ request()->routeIs('packings.index') ? 'active' : '' }}"
                                        href="{{ url('/settings/packings') }}">Packings</a>
                                </li>
                            @endcan
                            @can('view-currency-list')
                                <li>
                                    <a class="{{ request()->routeIs('currencies.index') ? 'active' : '' }}"
                                        href="{{ url('/settings/currencies') }}">Currencies</a>
                                </li>
                            @endcan
                            @can('view-spec-list')
                                <li>
                                    <a class="{{ request()->routeIs('specs.index') ? 'active' : '' }}"
                                        href="{{ url('/settings/specs') }}">Specs</a>
                                </li>
                            @endcan
                            @can('view-app-logs')
                                <li>
                                    <a class="{{ request()->routeIs('app-logs') ? 'active' : '' }}"
                                        href="{{ url('/settings/app-logs') }}">App Logs</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End: Sidebar -->

        <!-- End: Settings -->
        <div class="flex flex-col justify-between min-h-screen">
            <div>
                <!-- BEGIN: Header -->
                <!-- BEGIN: Header -->
                <div class="z-[9]" id="app_header">
                    <div
                        class="app-header z-[999] ltr:ml-[248px] rtl:mr-[248px] bg-white dark:bg-slate-800 shadow-sm dark:shadow-slate-700">
                        <div class="flex justify-between items-center h-full">
                            <div
                                class="flex items-center md:space-x-4 space-x-2 xl:space-x-0 rtl:space-x-reverse vertical-box">
                                <a href="{{ url('/') }}" class="mobile-logo xl:hidden inline-block">
                                    <img src="{{ asset('images/logo/pricing-logo.png') }}" class="black_logo"
                                        alt="logo">
                                    <img src="{{ asset('images/logo/pricing-logo.png') }}" class="white_logo"
                                        alt="logoo">
                                </a>


                            </div>
                            <!-- end vertcial -->
                            <div class="items-center space-x-4 rtl:space-x-reverse horizental-box">
                                <a href="{{ url('/') }}">
                                    <span class="xl:inline-block hidden">
                                        <img src="{{ asset('images/logo/pricing-logo.png') }}" class="black_logo "
                                            alt="logo">
                                        <img src="{{ asset('images/logo/pricing-logo.png') }}" class="white_logo"
                                            alt="logo">
                                    </span>
                                    <span class="xl:hidden inline-block">
                                        <img src="{{ asset('images/logo/pricing-logo.png') }}" class="black_logo "
                                            alt="logo">
                                        <img src="{{ asset('images/logo/pricing-logo.png') }}" class="white_logo "
                                            alt="logo">
                                    </span>
                                </a>
                                <button
                                    class="smallDeviceMenuController  open-sdiebar-controller xl:hidden inline-block">
                                    <iconify-icon
                                        class="leading-none bg-transparent relative text-xl top-[2px] text-slate-900 dark:text-white"
                                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>

                            </div>
                            <!-- end horizental -->




                            <!-- end top menu -->
                            <div
                                class="nav-tools flex items-center lg:space-x-5 space-x-3 rtl:space-x-reverse leading-0">

                                {{-- <!-- BEGIN: Toggle Theme -->
                                <div>
                                    <button id="themeMood"
                                        class="h-[28px] w-[28px] lg:h-[32px] lg:w-[32px] lg:bg-gray-500-f7 bg-slate-50 dark:bg-slate-900 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center">
                                        <iconify-icon class="text-slate-800 dark:text-white text-xl dark:block hidden"
                                            id="moonIcon" icon="line-md:sunny-outline-to-moon-alt-loop-transition">
                                        </iconify-icon>
                                        <iconify-icon class="text-slate-800 dark:text-white text-xl dark:hidden block"
                                            id="sunIcon" icon="line-md:moon-filled-to-sunny-filled-loop-transition">
                                        </iconify-icon>
                                    </button>
                                </div> --}}

                                <button id="addClient"
                                    class="h-[28px] w-[28px] lg:h-[32px] lg:w-[32px] lg:bg-gray-500-f7 bg-slate-50 dark:bg-slate-900 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center">
                                    <iconify-icon class="text-slate-800 dark:text-white text-xl dark:block hidden"
                                        id="moonIcon" icon="heroicons:user-plus">
                                    </iconify-icon>
                                </button>

                                <!-- BEGIN: Profile Dropdown -->
                                <!-- Profile DropDown Area -->
                                <div class="md:block hidden w-full">
                                    <button
                                        class="text-slate-800 dark:text-white focus:ring-0 focus:outline-none font-medium rounded-lg text-sm text-center    inline-flex items-center"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div
                                            class="lg:h-8 lg:w-8 h-7 w-7 rounded-full flex-1 ltr:mr-[10px] rtl:ml-[10px]">
                                            @if (Auth::user()->image_url)
                                                <img src="{{ Auth::user()->full_image_url }}"
                                                    class="w-full h-full object-cover rounded-full" alt="user image">
                                            @else
                                                <span
                                                    class="block w-full h-full object-cover text-center text-lg leading-8 user-initial">
                                                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span
                                            class="flex-none text-slate-600 dark:text-white text-sm font-normal items-center lg:flex hidden overflow-hidden text-ellipsis whitespace-nowrap">
                                            {{ ucwords(Auth::user()->username) }}
                                        </span>
                                        <svg class="w-[16px] h-[16px] dark:text-white hidden lg:inline-block text-base inline-block ml-[10px] rtl:mr-[10px]"
                                            aria-hidden="true" fill="none" stroke="currentColor"
                                            viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div
                                        class="dropdown-menu z-10 hidden bg-white divide-y divide-slate-100 shadow w-44 dark:bg-slate-800 border dark:border-slate-700 !top-[23px] rounded-md    overflow-hidden">
                                        <ul class="py-1 text-sm text-slate-800 dark:text-slate-200">
                                            <li>
                                                <a href="{{ url('/profile') }}" target="_blank"
                                                    class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600 dark:text-white font-normal">
                                                    <iconify-icon icon="heroicons-outline:user"
                                                        class="relative top-[2px] text-lg ltr:mr-1 rtl:ml-1">
                                                    </iconify-icon>
                                                    <span class="font-Inter">Profile</span>
                                                </a>
                                            </li>


                                            <li>
                                                <a href="{{ url('/logout') }}"
                                                    class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600 dark:text-white font-normal">
                                                    <iconify-icon icon="heroicons-outline:login"
                                                        class="relative top-[2px] text-lg ltr:mr-1 rtl:ml-1">
                                                    </iconify-icon>
                                                    <span class="font-Inter">Logout</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- END: Header -->
                                <button class="smallDeviceMenuController md:hidden block leading-0">
                                    <iconify-icon class="cursor-pointer text-slate-900 dark:text-white text-2xl"
                                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>
                                <!-- end mobile menu -->
                            </div>
                            <!-- end nav tools -->
                        </div>
                    </div>
                </div>

                <!-- BEGIN: Search Modal -->
                <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                    id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
                    <div class="modal-dialog relative w-auto pointer-events-none top-1/4">
                        <div
                            class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white dark:bg-slate-900 bg-clip-padding rounded-md outline-none text-current">
                            <form>
                                <div class="relative">
                                    <input type="text" class="form-control !py-3 !pr-12" placeholder="Search">
                                    <button
                                        class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l text-xl border-l-slate-200 dark:border-l-slate-600 dark:text-slate-300 flex items-center justify-center">
                                        <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- END: Search Modal -->
                <!-- END: Header -->
                <!-- END: Header -->
                <div class="content-wrapper transition-all duration-150 ltr:ml-[248px] rtl:mr-[248px]"
                    id="content_wrapper">
                    <div class="page-content">
                        <div class="transition-all duration-150 container-fluid" id="page_layout">
                            <div id="content_layout">



                                <!-- The actual SIMPLE-TOAST  -->
                                <div id="simpleToast">

                                </div>

                                <div class=" space-y-5">
                                    @yield('content')
                                    {{ $slot ?? '' }}
                                </div>
                                <livewire:components.info-modal />
                                <livewire:components.confirmation-modal />
                                <livewire:components.add-client-modal />

                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </main>
    <!-- scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/rt-plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        function confirmAndGoTo(msg, url, another_tab = false) {
            if (confirm(msg)) {
                return another_tab ? window.location.href = url : window.open(url, '_blank');
            }
        }

        function confirmDeleteAndGoTo(url) {
            return confirmAndGoTo('Are you sure yo want to delete ?', url)
        }
    </script>


    <script>
        function showToast(message, type) {
            var x = document.getElementById("simpleToast");

            let icon = "";

            if (type === "success") {
                x.style.backgroundColor = "#50C793";
                icon = '<iconify-icon icon="material-symbols:check"></iconify-icon>';
            } else if (type === "failed") {
                x.style.backgroundColor = "#F1595C";
                icon = '<iconify-icon icon="ph:warning"></iconify-icon>';
            } else if (type === "info") {
                x.style.backgroundColor = "black";
                icon = '<iconify-icon icon="material-symbols:info-outline"></iconify-icon>';
            } else {
                x.style.backgroundColor = "gray";
            }

            x.innerHTML = icon + "&nbsp;" + (message || " No message provided");
            x.className = "show";

            setTimeout(function() {
                x.className = x.className.replace("show", "");
            }, 3000);
        }

        // Fix for Livewire v3 dispatched events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toastalert', (data) => {
                showToast(data[0].message, data[0].type);
            });

            Livewire.on('copyUrl', (url) => {
                navigator.clipboard.writeText(url.url);
                showToast('URL copied to clipboard', 'success');
            });

            $('#addClient').click(function() {
                Livewire.dispatch('openNewClient');
            });
        });
    </script>

    @yield('child_scripts')
    @stack('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script> --}}
</body>

</html>
