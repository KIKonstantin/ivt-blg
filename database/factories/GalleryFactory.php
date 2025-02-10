<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {

        return [
            'name' => $this->faker->word(),
            'images' => json_encode([
                'https://picsum.photos/1920/1080?random=' . $this->faker->randomDigit(),
                'https://picsum.photos/1920/1080?random=' . $this->faker->randomDigit(),
                'https://picsum.photos/1920/1080?random=' . $this->faker->randomDigit(),
                'https://picsum.photos/1920/1080?random=' . $this->faker->randomDigit(),
                'https://picsum.photos/1920/1080?random=' . $this->faker->randomDigit()
            ])
        ];
    }
}
