<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
class AdvertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    { 
        #'name', 'link', 'title','description','counter','discount','code'
        $categories = Category::pluck('id')->toArray();
        
        
        return [
            'name' => $this->faker->word,
            'link' => $this->faker->url,
            'image' => $this->faker->imageUrl,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'counter' => $this->faker->numberBetween(10, 100),
            'discount' => $this->faker->numberBetween(5, 20),
            'code' => $this->faker->boolean,
            'category_id' => $this->faker->randomElement($categories),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
