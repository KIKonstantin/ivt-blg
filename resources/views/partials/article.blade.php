<article class="article__card">
    @isAdmin
        <form action="{{ route('article.destroy', $article->id) }}" method="POST" class="my-5">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">
                Изтрий статия
            </button>
        </form>
    @endIsAdmin
    
    <a href="{{ route('articles.show', $article) }}" class="article__content">
        <img class="article__card__image" src="{{ $article->blog_image }}" alt="{{ $article->title }}" loading="lazy" />
        <div>
            <h3 class="">{{ $article->title }}</h3>
            @isset($article->description)
                <p class="">
                    {{ $article->description }}
                </p>
            @endisset
            <strong>
                #{{$article->category?->name}}
            </strong>
        </div>

    </a>
    
</article>