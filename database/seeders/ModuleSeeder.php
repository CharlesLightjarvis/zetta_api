<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $formations = Formation::all();
        $faker = Faker::create();

        // Créer quelques modules
        $modules = [];
        for ($i = 0; $i < 10; $i++) {
            $modules[] = Module::create([
                'name' => $faker->sentence(4),
                'slug' => $faker->unique()->slug,
                'description' => $faker->paragraph,
            ]);
        }

        // Attacher des modules aléatoires aux formations
        foreach ($formations as $formation) {
            $randomModules = collect($modules)->random(rand(2, 4));
            $formation->modules()->attach($randomModules);
        }
    }
}
