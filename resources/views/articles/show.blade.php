@extends('layouts.master')
@section('content')
<section class="article-page">
<div class="container">
    <div class="row">
        <div class="col-12">
            <img class="article__main-img" src="{{asset($article->blog_image)}}" alt="{{$article->title}}">

            <h1 class="article__title">{{$article->title}}</h1>
            <article class="article__content">{!!$article->content!!}</article>
        </div>
    </div>
</div>
</section>
@isAdmin
    <a class="block my-12 text-xl" href="{{route('article.edit', $article)}}">Редактирай статия</a>
@endIsAdmin
@push('styles')
    @vite('resources/styles/pages/article.sass')
@endpush
@endsection
