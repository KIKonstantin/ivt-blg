<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Great+Vibes&display=swap"
        rel="stylesheet">
    <!-- Lenis -->
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.20/dist/lenis.css">
    @vite(['resources/styles/app.css', 'resources/styles/app.sass'])
    <title>По Петите На Дедите</title>
    @yield('head')

</head>

<body>
    @include('partials.header')
    <main>
        @yield('content')
    </main>
    <footer>
        Благодаря за вниманието скъпи читатели
    </footer>
    <!-- Lenis Smoothscroll -->
    <script src="https://unpkg.com/lenis@1.1.20/dist/lenis.min.js"></script>
    <script>const lenis = new Lenis({autoRaf: true});</script>
    
    @stack('styles')
    @stack('scripts')
</body>

</html>
