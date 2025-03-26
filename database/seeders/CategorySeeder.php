<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(10)->create();

        Category::factory(20)->create([
            'parent_id' => Category::inRandomOrder()->first()->id,
        ]);
    }
}
