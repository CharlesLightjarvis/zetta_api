<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $formations = Formation::all();
        $faker = Faker::create();

        foreach ($formations as $formation) {
            for ($i = 0; $i < 3; $i++) { // 3 modules par formation
                Module::create([
                    'name' => $faker->sentence(4),
                    'slug' => $faker->slug,
                    'description' => $faker->paragraph,
                    'formation_id' => $formation->id,
                ]);
            }
        }
    }
}
