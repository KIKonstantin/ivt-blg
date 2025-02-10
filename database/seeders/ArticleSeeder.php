<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Gallery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = Gallery::all();
        $categories = Category::all();

        Article::factory(10)->create([
            'gallery_id' => $galleries->random()->id,
            'category_id' => $categories->random()->id
        ]);
    }
}
