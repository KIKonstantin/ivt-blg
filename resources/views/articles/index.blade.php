
@extends('layouts.master')
@section('content')

<section class="container-fluid hero">
    <hgroup>
        <span class="h3" data-parallax="-0.1">Блог</span>
        <h1 class="hero__title big" data-parallax="-0.2">
            <span>По</span>
            <span>петите</span> 
            <span>на</span> 
            <span>дедите</span>
        </h1>
        <h2 class="h1">Ивета Василева</h2>
    </hgroup>
</section>
<section class="container">
    <div class="listing">
        @foreach($articles as $article)
            @include('partials.article')
        @endforeach
    </div>
    @isset($categories)
        @foreach($categories as $category)
            <div>
                <a class="category" href="{{route('category', $category)}}">
                    {{$category->name}}
                </a>
            </div>
        @endforeach
    @endisset
</section>
@endsection

@push('scripts')
    @vite(['resources/js/homepage.js', 'resources/js/scroll.js'])
@endpush
  