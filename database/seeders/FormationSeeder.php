<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Formation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = Category::all();
        $faker = Faker::create();

        // Liste de prérequis et objectifs possibles
        $possiblePrerequisites = [
            'Connaissances de base en programmation',
            'Bases en algorithmique',
            'Expérience en développement web',
            'Connaissances en bases de données',
            'Compétences en mathématiques',
            'Expérience en gestion de projet',
            'Connaissances en réseaux',
            'Expérience en cloud computing',
            'Compétences en cybersécurité',
            'Connaissances en intelligence artificielle',
        ];

        $possibleObjectives = [
            'Maîtriser les concepts avancés',
            'Appliquer les bonnes pratiques',
            'Développer des applications modernes',
            'Gérer des infrastructures complexes',
            'Analyser des données massives',
            'Concevoir des interfaces utilisateur intuitives',
            'Automatiser des processus métier',
            'Sécuriser des systèmes informatiques',
            'Développer des jeux vidéo',
            'Administrer des systèmes et serveurs',
        ];

        for ($i = 0; $i < 20; $i++) {
            // Générer des prérequis et objectifs aléatoires
            $prerequisites = $faker->randomElements($possiblePrerequisites, $faker->numberBetween(1, 3));
            $objectives = $faker->randomElements($possibleObjectives, $faker->numberBetween(1, 3));

            Formation::create([
                'name' => $faker->sentence(6),
                'slug' => Str::slug($faker->sentence(6)),
                'description' => $faker->paragraph,
                'duration' => $faker->numberBetween(4, 16),
                'price' => $faker->numberBetween(500, 3000),
                'discount_price' => $faker->numberBetween(500, 3000),
                'level' => $faker->randomElement(['beginner', 'intermediate', 'advanced']),
                'category_id' => $categories->random()->id,
                'prerequisites' => $prerequisites, // Prérequis aléatoires
                'objectives' => $objectives,      // Objectifs aléatoires
            ]);
        }
    }
}
