@extends('layouts.master')

@section('content')

<article class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="text-center mb-12">
        <div class="mb-4">
            @if($article->category)
                <a href="{{ route('category', $article->category) }}" class="inline-block px-4 py-1 rounded-full border border-stone-200 text-xs font-bold text-amber-600 uppercase tracking-widest hover:border-amber-600 transition duration-300">
                    {{ $article->category->name }}
                </a>
            @endif
        </div>
        <h1 class="font-display text-5xl md:text-6xl text-stone-900 mb-6 leading-tight">{{ $article->title }}</h1>
        <div class="flex justify-center items-center text-stone-500 font-serif italic text-sm space-x-4">
            <span>{{ $article->created_at->format('d F Y') }}</span>
            <span>&bull;</span>
            <span>Ивета Костадинова</span>
        </div>
    </div>

    {{-- Main Image --}}
    @if($article->blog_image)
    <div class="mb-12 rounded-xl overflow-hidden shadow-lg h-[400px] md:h-[600px] relative">
         <img class="w-full h-full object-cover" src="{{ Str::startsWith($article->blog_image, 'http') ? $article->blog_image : Storage::url($article->blog_image) }}" alt="{{ $article->title }}">
    </div>
    @endif

    {{-- Content --}}
    <div class="prose prose-stone prose-lg md:prose-xl mx-auto font-serif prose-headings:font-display prose-headings:font-normal prose-a:text-amber-700 hover:prose-a:text-amber-600">
        {!! $article->content !!}
    </div>
    
    {{-- Admin Actions --}}
    @auth
        @if(auth()->user()->is_admin)
            <div class="mt-12 pt-8 border-t border-stone-200 flex justify-end">
                <a href="{{ route('article.edit', $article) }}" class="inline-flex items-center px-4 py-2 bg-stone-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-stone-700 focus:bg-stone-700 active:bg-stone-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Редактирай статия
                </a>
            </div>
        @endif
    @endauth

</article>

@endsection
