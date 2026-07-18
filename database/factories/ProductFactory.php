<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'category_id' => Category::factory(),
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'sku'         => strtoupper(fake()->unique()->bothify('??-###')),
            'description' => fake()->sentence(),
            'price'       => fake()->numberBetween(5000, 100000),
            'stock'       => fake()->numberBetween(1, 100),
            'weight'      => fake()->numberBetween(100, 2000),
            'is_active'   => true,
        ];
    }
}
