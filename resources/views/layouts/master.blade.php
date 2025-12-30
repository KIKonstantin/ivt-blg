<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    @vite(['resources/styles/app.css', 'resources/js/app.js'])
    <title>Short Trips Around - Travel Blog</title>
    @yield('head')

</head>

<body class="font-sans text-gray-800 bg-stone-50 flex flex-col min-h-screen">
    @include('partials.header')
    
    <main class="flex-grow container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-stone-900 text-stone-300 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p class="font-serif italic text-lg mb-2">Благодаря за вниманието, скъпи читатели</p>
            <p class="text-sm text-stone-500">&copy; {{ date('Y') }} Short Trips Around. All rights reserved.</p>
        </div>
    </footer>
    
    @stack('styles')
    @stack('scripts')
</body>

</html>
