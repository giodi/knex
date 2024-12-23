<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Deskriptorenportal Burgerbibliothek Bern @yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <header class="is-fixed-above-lg is-fixed">
        <nav class="container-fluid">
            <ul>
                <li><a href="/" class="contrast">Start</a></li>
                <li><a href="/list" class="contrast">Liste</a></li>
                <li><a href="/timeline" class="contrast">Zeitstrahl</a></li>
            </ul>
        </nav>
    </header>
    <main class="container-fluid">
        @yield('main')
    </main>
</body>

</html>
