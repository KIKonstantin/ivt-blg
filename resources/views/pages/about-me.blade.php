@extends('layouts.master')

@section('head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        .reveal-text { clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%); line-height: 1.15; }
        .char { display: inline-block; }
        .image-parallax { will-change: transform; }
        .cursor-follow { pointer-events: none; mix-blend-mode: difference; z-index: 9999; }
    </style>
@endsection

@section('content')

{{-- Hero Section --}}
<div class="relative h-screen flex items-center justify-center overflow-hidden bg-stone-900 text-stone-100">
    <div class="absolute inset-0 image-parallax opacity-60">
        <img src="https://images.unsplash.com/photo-1500835556837-99ac94a94552?q=80&w=2576&auto=format&fit=crop" class="w-full h-full object-cover scale-110" alt="Travel Background">
    </div>
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative z-10 text-center px-4">
        <p class="font-serif italic text-xl md:text-2xl mb-4 opacity-0 hero-anim">Здравейте, аз съм</p>
        <h1 class="font-display text-7xl md:text-9xl mb-6 text-white leading-tight opacity-0 hero-anim translate-y-10">
            Iveta
        </h1>
        <p class="font-sans text-stone-300 max-w-xl mx-auto text-lg opacity-0 hero-anim translate-y-5">
            Пътешественик. Фотограф. Разказвач на истории.
        </p>
    </div>
    
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
    </div>
</div>

{{-- Intro / Story Section --}}
<section class="py-24 md:py-32 bg-stone-50 px-4 overflow-hidden relative" id="intro-section">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
        <div class="relative">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-amber-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-indigo-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="relative rounded-2xl overflow-hidden shadow-2xl reveal-image-trigger">
                <img src="https://images.unsplash.com/photo-1527631746610-bca00a040d60?q=80&w=1974&auto=format&fit=crop" class="w-full h-auto object-cover transform scale-110 reveal-image" alt="Profile of Iveta">
            </div>
        </div>
        <div>
            <h2 class="font-display text-5xl md:text-6xl text-stone-900 mb-8 split-text">Моята История</h2>
            <div class="prose prose-lg text-stone-600 font-serif leading-relaxed stagger-text">
                <p class="mb-6">
                    Всичко започна с една карта на света, закачена на стената в детската ми стая. Винаги съм се чудила какво се крие отвъд хоризонта, какъв е вкусът на въздуха в непознати земи и как звучат стъпките по древни калдъръми.
                </p>
                <p class="mb-6">
                    Аз не просто пътувам – аз колекционирам моменти. Търся душата на всяко място, от мъгливите върхове на Родопите до тюркоазените води на Средиземноморието. За мен пътуването е изкуството да се губиш, за да намериш себе си.
                </p>
                <p>
                    Този блог е моят дигитален дневник. Тук споделям не само маршрути и съвети, но и емоциите, които всяко пътешествие пробужда в мен. Добре дошли в моя свят!
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Philosophy Section with Parallax Horizontal --}}
<section class="py-24 bg-stone-900 text-stone-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 mb-16">
        <h2 class="font-display text-5xl md:text-7xl text-center text-white mb-4 reveal-up">Философия</h2>
        <p class="text-center text-stone-400 font-serif italic text-xl reveal-up">"Светът е книга и тези, които не пътуват, четат само една страница."</p>
    </div>

    <div class="flex flex-col md:flex-row justify-center items-center gap-8 md:gap-16 px-4">
        <div class="w-full md:w-1/3 philosophy-card opacity-0 translate-y-20">
            <div class="h-80 overflow-hidden rounded-lg mb-6 shadow-xl group">
                <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2021&auto=format&fit=crop" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Freedom">
            </div>
            <h3 class="text-2xl font-display text-amber-200 mb-3">Свобода</h3>
            <p class="text-stone-300 font-light">
                Пътуването е свободата да бъдеш всеки ден на различно място, да се будиш с непознати изгреви и да заспиваш под нови звезди.
            </p>
        </div>
        <div class="w-full md:w-1/3 philosophy-card opacity-0 translate-y-20" style="transition-delay: 150ms;">
            <div class="h-80 overflow-hidden rounded-lg mb-6 shadow-xl group">
                <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Nature">
            </div>
            <h3 class="text-2xl font-display text-amber-200 mb-3">Природа</h3>
            <p class="text-stone-300 font-light">
                Връзката с природата е най-чистата форма на терапия. Планината ме учи на смирение, а морето – на безкрайност.
            </p>
        </div>
        <div class="w-full md:w-1/3 philosophy-card opacity-0 translate-y-20" style="transition-delay: 300ms;">
            <div class="h-80 overflow-hidden rounded-lg mb-6 shadow-xl group">
                <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Culture">
            </div>
            <h3 class="text-2xl font-display text-amber-200 mb-3">Култура</h3>
            <p class="text-stone-300 font-light">
                Хората са сърцето на всяко място. Обичам да слушам техните истории, да опитвам храната им и да разбирам техния свят.
            </p>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-24 bg-stone-100 stats-section">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="stat-item">
                <span class="block text-5xl md:text-7xl font-display text-stone-800 counter" data-target="27">0</span>
                <span class="text-stone-500 uppercase tracking-widest text-xs mt-2">Страни</span>
            </div>
            <div class="stat-item">
                <span class="block text-5xl md:text-7xl font-display text-stone-800 counter" data-target="154">0</span>
                <span class="text-stone-500 uppercase tracking-widest text-xs mt-2">Градове</span>
            </div>
            <div class="stat-item">
                <span class="block text-5xl md:text-7xl font-display text-stone-800 counter" data-target="3500">0</span>
                <span class="text-stone-500 uppercase tracking-widest text-xs mt-2">Снимки</span>
            </div>
            <div class="stat-item">
                <span class="block text-5xl md:text-7xl font-display text-stone-800 counter" data-target="892">0</span>
                <span class="text-stone-500 uppercase tracking-widest text-xs mt-2">Кафета</span>
            </div>
        </div>
    </div>
</section>

{{-- Contact CTA --}}
<section class="py-32 bg-white text-center px-4">
    <div class="max-w-3xl mx-auto">
        <h2 class="font-display text-5xl md:text-6xl text-stone-900 mb-8 reveal-up">Искате ли да работим заедно?</h2>
        <p class="text-stone-600 text-lg mb-10 font-serif italic reveal-up">
            "Пътуването е единственото нещо, което купуваш и те прави по-богат."
        </p>
        <a href="mailto:iveta@example.com" class="inline-block px-8 py-4 bg-stone-900 text-white font-semibold rounded-full hover:bg-stone-800 transition-all hover:scale-105 duration-300 reveal-up">
            Свържете се с мен
        </a>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        gsap.registerPlugin(ScrollTrigger);

        // Hero Animations
        gsap.to('.hero-anim', {
            y: 0,
            opacity: 1,
            duration: 1.5,
            stagger: 0.2,
            ease: "power3.out"
        });

        // Parallax Effect for Hero Image
        gsap.to('.image-parallax img', {
            yPercent: 20,
            ease: "none",
            scrollTrigger: {
                trigger: ".image-parallax",
                start: "top top",
                end: "bottom top",
                scrub: true
            }
        });

        // Intro Image Reveal
        gsap.to('.reveal-image', {
            scale: 1,
            duration: 1.5,
            ease: "power2.out",
            scrollTrigger: {
                trigger: ".reveal-image-trigger",
                start: "top 80%",
            }
        });

        // Text Stagger for Intro
        gsap.from('.stagger-text p', {
            y: 30,
            opacity: 0,
            duration: 1,
            stagger: 0.2,
            scrollTrigger: {
                trigger: ".stagger-text",
                start: "top 85%"
            }
        });

        // Philosophy Cards
        gsap.to('.philosophy-card', {
            opacity: 1,
            y: 0,
            duration: 1,
            stagger: 0.2,
            ease: "power3.out",
            scrollTrigger: {
                trigger: ".philosophy-card",
                start: "top 85%"
            }
        });

        // Counters
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            gsap.to(counter, {
                innerText: target,
                duration: 2,
                snap: { innerText: 1 },
                scrollTrigger: {
                    trigger: counter,
                    start: "top 85%",
                    once: true
                }
            });
        });

        // Reveal Up General
        gsap.utils.toArray('.reveal-up').forEach(elem => {
            gsap.from(elem, {
                y: 50,
                opacity: 0,
                duration: 1,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: elem,
                    start: "top 90%"
                }
            });
        });

        // Optional: Custom smooth scroll (lenis) could be added here for "awwwards" feel
        // but standardized browser scrolling with GSAP scrub is often safer for minimal setups.
    });
</script>

@endsection