<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>Login</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/genuine-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/rt-plugins.css') }}">
    <link href="https://unpkg.com/aos@2.3.0/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- START : Theme Config js-->
    <script src="{{ asset('js/settings.js') }}" sync></script>
    <!-- END : Theme Config js-->
    @livewireStyles
</head>

<body class=" font-inter skin-default">
    <div>

        <div id="simpleToast"></div>

        {{ $slot ?? '' }}

    </div>
    <!-- scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/rt-plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts

    <script>
        // Fix for Livewire v3 dispatched events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toastalert', (data) => {
                console.log('Toast event received:', data);
                var x = document.getElementById("simpleToast");

                const {
                    message,
                    type
                } = data[0];

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
            });
        });
    </script>
</body>

</html>
