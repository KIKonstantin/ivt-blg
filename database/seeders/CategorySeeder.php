<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => "Планина", 'slug' => 'mountain'],
            ['name' => "Море", 'slug' => 'sea'],
            ['name' => "Градски туризъм", 'slug' => 'city-tourism'],
            ['name' => "Култура и История", 'slug' => 'culture-history'],
            ['name' => "Екзотика", 'slug' => 'exotic'],
        ]);
    }
}
