@extends('admin.layout')

@section('content')
<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-64 bg-stone-900 text-stone-300 flex flex-col">
        <div class="p-6 border-b border-stone-800">
            <h1 class="font-display text-2xl text-white">Admin Panel</h1>
            <span class="text-xs text-stone-500 uppercase tracking-widest">Short Trips Around</span>
        </div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded text-stone-100 bg-stone-800 font-semibold">
                Dashboard
            </a>
            <a href="{{ route('articles.index') }}" target="_blank" class="block px-4 py-2 rounded hover:bg-stone-800 transition-colors">
                View Site
            </a>
        </nav>
        <div class="p-4 border-t border-stone-800">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 rounded text-stone-400 hover:text-white hover:bg-red-900/50 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-grow bg-stone-50 p-8 overflow-y-auto h-screen">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-display text-stone-800">Manage Stories</h2>
                <a href="{{ route('articles.create') }}" class="px-6 py-2 bg-stone-800 text-white rounded-md font-semibold hover:bg-stone-700 transition-colors shadow-sm">
                    + New Article
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider font-serif">
                                Title / Slug
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider font-serif">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider font-serif">
                                Published
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-stone-500 uppercase tracking-wider font-serif">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @foreach($articles as $article)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($article->blog_image)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Str::startsWith($article->blog_image, 'http') ? $article->blog_image : Storage::url($article->blog_image) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-stone-200 flex items-center justify-center text-stone-500 text-xs">No</div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-stone-900">{{ $article->title }}</div>
                                        <div class="text-sm text-stone-500">{{ $article->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 uppercase tracking-wide">
                                    {{ $article->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500 font-serif italic">
                                {{ $article->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('article.edit', $article) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('article.destroy', $article) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
