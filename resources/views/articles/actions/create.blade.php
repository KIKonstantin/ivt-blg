
@extends('layouts.master')
@section('head')
<!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/33cdpuxwivnwgdqphjwbs9ptbaipockgm73d1suai5l13x68/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
     tinymce.init({
        selector: 'textarea#content',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        images_upload_url: '/upload-image',
        automatic_uploads: true,
        images_upload_credentials: true

    });

    </script>
@endsection
@section('content')
<section>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <div role="alert">
        <div class="bg-red-500 text-white font-bold rounded-t px-4 py-2">
          Danger
        </div>
        <ul class="border border-t-0 border-red-400 rounded-b bg-red-100 px-4 py-3 text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</>
            @endforeach
        </div>
      </div>
@endif

    <form action="{{route('article.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method("POST")
        <div>
            <label for="title">Заглавие на Блог Статията</label>
            <input id="title" type="text" name="title">
        </div>
        <div>
            <label for="description">Кратко описание на статията</label>
            <textarea id="description" type="text" name="description"></textarea>
        </div>
        <div>
            <label for="content">Съдържание на Блог Статията</label>
            <textarea id="content" name="content"></textarea>
        </div>
        <div>
            <label for="slug">URL address на Блог Статията</label>
            <input id="slug" type="text" name="slug">
        </div>
        <div>
            <label for="blog_post_image">Основно изображение на блог статията</label>
            <input id="blog_post_image" type="file" name="blog_post_image" accept="image/*"/>
        </div>
        <div>
            <label for="gallery">Галерия</label>
            <input id="gallery" type="file" multiple name="gallery_images" accept="image/*"/>
        </div>
        <div>
            <label for="category_pick">Избери Категория на статията</label>
            <select name="category_id" id="category_id">
                <option disabled>Избери категория на блог статията</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{$category->name}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">POST</button>
    </form>
</section>
@endsection
