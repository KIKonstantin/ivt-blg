@extends('admin.layout')
@section('content')
    <main>
        <h1>
            Welcome, Iveta. This is your admin panel and this is your dashboard.
        </h1>
    </main>

    <aside>
        <form action="{{ route('admin.logout') }}" method="POST" class="flex">
            @csrf
            <button type="submit"
                class="bg-transparent mt-40 mx-auto hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">Излез</button>
        </form>
    </aside>
@endsection
