<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all();
        $categories = Category::all();

        for ($i = 1; $i <= 1200; $i++) {
            Post::create([
                'title' => "Sample Post Title {$i}",
                'body' => "This is the body content for post {$i}. Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                'slug' => "sample-post-title-{$i}",
                'category_id' => $categories->random()->id,
                'author_id' => $users->random()->id,
                'status' => true
            ]);
        }
    }
}

