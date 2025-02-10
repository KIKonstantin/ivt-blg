<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(8, true), // Ensure paragraphs are formatted correctly
            'slug' => $this->faker->slug(),
            'blog_image' => 'https://picsum.photos/1920/1080?random=' . $this->faker->randomDigit(),
        ]; 
    }
}
