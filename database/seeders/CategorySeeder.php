<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $name = fake()->unique()->words(2, true);

            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => 1,
            ]);
        }
    }
}
