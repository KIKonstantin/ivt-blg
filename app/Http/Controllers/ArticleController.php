<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        $categories = Category::all();
        return view('articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('articles.actions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'category_id' => intval($request->category_id),
        ]);


        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'blog_post_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:articles',
        ]);

        if ($request->hasFile('blog_post_image')) {
            $imagePath = $request->file('blog_post_image')->store('blog_image', 'public');
            $validated['blog_image'] = $imagePath;
        }

        Article::create($validated);

        return redirect()->route('articles.index')->with('success', 'Article created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $categories = Category::all();
        return view('articles.actions.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $request->merge([
            'category_id' => intval($request->category_id),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'blog_post_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:articles,slug,' . $article->id,
        ]);

        if ($request->hasFile('blog_post_image')) {
            // Delete old image if exists
            if ($article->blog_image) {
                Storage::disk('public')->delete($article->blog_image);
            }
            $imagePath = $request->file('blog_post_image')->store('blog_image', 'public');
            $validated['blog_image'] = $imagePath;
        }

        $article->update($validated);

        return redirect()->route('articles.show', ['article' => $article->slug])->with('success', 'Blog updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->blog_image) {
            Storage::disk('public')->delete($article->blog_image);
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', "Article has been successfully deleted");
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('article_images', 'public');

            // Връщаме JSON с линка, който TinyMCE очаква
            return response()->json([
                'location' => Storage::url($path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }
}
