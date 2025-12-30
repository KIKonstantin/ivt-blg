
@extends('admin.layout')

@section('content')
<div class="min-h-screen bg-stone-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-display text-stone-900">Edit Article</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-stone-600 hover:text-stone-900 flex items-center">
                &larr; Back to Dashboard
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-stone-200">
            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Oops!</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('article.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="col-span-2">
                            <label for="title" class="block text-sm font-medium text-stone-700">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" 
                                   class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border p-2">
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="slug" class="block text-sm font-medium text-stone-700">Slug (URL)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $article->slug) }}" 
                                   class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border p-2">
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-stone-700">Category</label>
                            <select id="category_id" name="category_id" 
                                    class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border p-2 bg-white">
                                <option value="" disabled>Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                        {{$category->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-stone-700">Short Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  class="mt-1 block w-full rounded-md border-stone-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm border p-2">{{ old('description', $article->description) }}</textarea>
                    </div>

                    {{-- Main Image --}}
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Main Image</label>
                        @if($article->blog_image)
                            <div class="mb-2">
                                <img src="{{ Str::startsWith($article->blog_image, 'http') ? $article->blog_image : Storage::url($article->blog_image) }}" alt="Current Image" class="h-32 rounded-md object-cover border border-stone-200">
                                <p class="text-xs text-stone-500 mt-1">Current Image</p>
                            </div>
                        @endif
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-stone-300 border-dashed rounded-md hover:border-amber-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-stone-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-stone-600">
                                    <label for="blog_post_image" class="relative cursor-pointer bg-white rounded-md font-medium text-amber-600 hover:text-amber-500 focus-within:outline-none">
                                        <span>Change Image</span>
                                        <input id="blog_post_image" name="blog_post_image" type="file" accept="image/*" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-stone-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Content (TinyMCE) --}}
                    <div>
                        <label for="content" class="block text-sm font-medium text-stone-700 mb-2">Content</label>
                        <textarea id="content" name="content" class="min-h-[400px]">{{ old('content', $article->content) }}</textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end pt-5">
                        <button type="button" onclick="window.history.back()" class="bg-white py-2 px-4 border border-stone-300 rounded-md shadow-sm text-sm font-medium text-stone-700 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 mr-3">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-stone-800 hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500">
                            Update Article
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/33cdpuxwivnwgdqphjwbs9ptbaipockgm73d1suai5l13x68/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#content',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    });
</script>
@endsection


