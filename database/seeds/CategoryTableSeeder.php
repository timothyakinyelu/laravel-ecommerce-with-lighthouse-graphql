<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $now = Carbon::now()->toDateTimeString();

        Category::insert([
            ['parent_id' => null, 'name' => 'Appliances', 'slug' => 'appliances', 'is_published' => 0],
            ['parent_id' => null, 'name' => 'Computers', 'slug' => 'computers', 'is_published' => 0],
            ['parent_id' => null, 'name' => 'Electronics', 'slug' => 'electronics', 'is_published' => 0],
            ['parent_id' => 2, 'name' => 'Laptops', 'slug' => 'laptops', 'is_published' => 0],
            ['parent_id' => 2, 'name' => 'Desktops', 'slug' => 'desktops', 'is_published' => 0],
            ['parent_id' => null, 'name' => 'Mobile Phones', 'slug' => 'mobile-phones', 'is_published' => 0],
            ['parent_id' => null, 'name' => 'Tablets', 'slug' => 'tablets', 'is_published' => 0],
            ['parent_id' => 3, 'name' => 'TVs', 'slug' => 'tvs', 'is_published' => 0],
            ['parent_id' => 3, 'name' => 'Digital Cameras', 'slug' => 'digital-cameras', 'is_published' => 0],
        ]);
    }
}
