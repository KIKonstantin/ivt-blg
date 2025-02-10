@extends('layouts.master')
@section('content')
<img src="{{asset($article->blog_image)}}" alt="{{$article->title}}">

<h1>{{$article->title}}</h1>
<article>{!!$article->content!!}</article>
@isAdmin
    <a class="block my-12 text-xl" href="{{route('article.edit', $article)}}">Редактирай статия</a>
@endIsAdmin
@endsection
