<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
    public function show(Category $category){
        $articles = $category->articles;
        return view('articles.index', compact('articles'));
    }
}
