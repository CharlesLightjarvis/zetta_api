<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $modules = Module::all();
        $faker = Faker::create();

        foreach ($modules as $module) {
            for ($i = 0; $i < 5; $i++) { // 5 leçons par module
                Lesson::create([
                    'name' => $faker->sentence(3),
                    'slug' => $faker->slug,
                    'description' => $faker->paragraph,
                    'module_id' => $module->id,
                ]);
            }
        }
    }
}
