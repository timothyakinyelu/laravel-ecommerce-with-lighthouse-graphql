<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence,
        'slug' => $faker->slug,
        'is_published' => 0,
        'description' => $faker->paragraph,
        'image' => 'products/dummy/laptop-1.jpg',
    ];
});
