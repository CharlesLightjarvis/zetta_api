<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Développement web',
                'description' => 'Catégorie de formations liées au développement web',
            ],
            [
                'name' => 'Devops',
                'description' => 'Catégorie de formations liées au devops',
            ],
            [
                'name' => 'Testing',
                'description' => 'Catégorie de formations liées au testing',
            ],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            Category::create($category);
        }
    }
}
