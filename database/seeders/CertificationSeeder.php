<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Formation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $formations = Formation::all();
        $faker = Faker::create();

        foreach ($formations as $formation) {
            Certification::create([
                'name' => $faker->sentence(3),
                'slug' => $faker->slug,
                'description' => $faker->paragraph,
                'validity_period' => $faker->numberBetween(1, 5),
                'provider' => $faker->company,
                'formation_id' => $formation->id,
            ]);
        }
    }
}
