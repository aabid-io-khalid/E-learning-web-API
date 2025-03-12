<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3), 
            'description' => $this->faker->paragraph, 
            'duration' => $this->faker->numberBetween(30, 300), 
            'level' => $this->faker->randomElement(['Beginner','Intermediate','Advanced']), 
            'status' => $this->faker->randomElement(['open', 'in progress', 'done']), 
            'category_id' => Category::inRandomOrder()->first()->id, 
            'sub_category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
