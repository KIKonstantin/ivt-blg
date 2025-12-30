<header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-stone-100">
    <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center">
        <div class="logo mb-4 md:mb-0">
            <a href="{{ route('articles.index') }}" class="font-display text-4xl text-stone-800 hover:text-stone-600 transition duration-300">
                Ивета Василева
            </a>
            <span class="block text-xs font-serif tracking-widest text-stone-500 text-center md:text-left uppercase mt-1">Short Trips Around</span>
        </div>
        
        <nav class="flex items-center space-x-8 font-serif text-lg text-stone-600">
            <a href="{{ route('articles.index') }}" class="hover:text-stone-900 transition-colors {{ request()->routeIs('articles.index') ? 'text-stone-900 font-semibold border-b-2 border-stone-800' : '' }}">
                Начало
            </a>
            <a href="{{ route('about-me') }}" class="hover:text-stone-900 transition-colors {{ request()->routeIs('about-me') ? 'text-stone-900 font-semibold border-b-2 border-stone-800' : '' }}">
                За мен
            </a>
        </nav>
    </div>
</header>

