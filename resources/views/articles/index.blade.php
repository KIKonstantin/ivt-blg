
@extends('layouts.master')

@section('content')

{{-- Hero Section --}}
<section class="relative h-[60vh] min-h-[400px] mb-12 rounded-2xl overflow-hidden shadow-xl group">
    <div class="absolute inset-0 bg-stone-900/40 group-hover:bg-stone-900/30 transition duration-500 z-10"></div>
    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2021&auto=format&fit=crop" 
         alt="Travel Hero" 
         class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
    <div class="absolute inset-0 z-20 flex flex-col justify-center items-center text-center text-white p-4">
        <h1 class="font-display text-6xl md:text-8xl mb-4 drop-shadow-md">Добре дошли</h1>
        <p class="font-serif text-xl md:text-2xl max-w-2xl italic drop-shadow-md">
            Открий света през моите очи. История, култура и незабравими моменти.
        </p>
    </div>
</section>

{{-- Categories Filter (Optional) --}}
@isset($categories)
<div class="flex flex-wrap justify-center gap-4 mb-12">
    @foreach($categories as $category)
        <a href="{{ route('category', $category) }}" 
           class="px-6 py-2 rounded-full border border-stone-300 bg-white text-stone-600 font-serif text-sm uppercase tracking-wide hover:bg-stone-800 hover:text-white transition duration-300 shadow-sm">
            {{$category->name}}
        </a>
    @endforeach
</div>
@endisset

{{-- Article Listings --}}
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
    @foreach($articles as $article)
        <article class="flex flex-col bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 border border-stone-100 group">
            <a href="{{ route('articles.show', $article->slug) }}" class="block overflow-hidden h-64 relative">
                @if($article->blog_image)
                    <img src="{{ Str::startsWith($article->blog_image, 'http') ? $article->blog_image : Storage::url($article->blog_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                @else
                    <div class="w-full h-full bg-stone-200 flex items-center justify-center text-stone-400">
                        <span class="font-serif italic">No image</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-stone-900/0 group-hover:bg-stone-900/10 transition duration-300"></div>
            </a>
            
            <div class="p-6 flex flex-col flex-grow">
                <div class="mb-4">
                    @if($article->category)
                        <span class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2 block">
                            {{ $article->category->name }}
                        </span>
                    @endif
                    <h2 class="font-display text-3xl text-stone-800 leading-tight mb-2 group-hover:text-amber-700 transition duration-300">
                        <a href="{{ route('articles.show', $article->slug) }}">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <div class="text-stone-500 text-sm font-serif italic mb-4">
                        {{ $article->created_at->format('d M, Y') }}
                    </div>
                </div>
                
                <div class="text-stone-600 font-serif leading-relaxed line-clamp-3 mb-6">
                   {!! strip_tags($article->description ?? Str::limit($article->content, 150)) !!}
                </div>
                
                <div class="mt-auto">
                    <a href="{{ route('articles.show', $article->slug) }}" class="inline-block text-stone-800 font-semibold uppercase text-xs tracking-widest border-b-2 border-amber-400 hover:border-amber-600 pb-1 transition-colors">
                        Прочети още
                    </a>
                </div>
            </div>
        </article>
    @endforeach
</section>

@endsection
  