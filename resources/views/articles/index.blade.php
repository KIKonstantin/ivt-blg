
@extends('layouts.master')
@section('content')


<x-homepage.hero />
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
    {{-- @vite(['resources/js/homepage.js', 'resources/js/scroll.js']) --}}
@endpush
  