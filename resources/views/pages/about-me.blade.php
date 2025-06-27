
@extends('layouts.master')
@section('content')
<x-homepage.about-me />
@endsection

@push('scripts')
    {{-- @vite(['resources/js/homepage.js', 'resources/js/scroll.js']) --}}
@endpush
  