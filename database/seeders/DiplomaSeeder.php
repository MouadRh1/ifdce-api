<?php

namespace Database\Seeders;

use App\Models\Diploma;
use Illuminate\Database\Seeder;

class DiplomaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diplomas = [
            [
                'name' => 'Licence en Informatique',
                'description' => 'Formation complète en développement logiciel, bases de données et réseaux',
                'duration_years' => 3,
                'level' => 'bachelor',
                'is_active' => true,
            ],
            [
                'name' => 'Master en Intelligence Artificielle',
                'description' => 'Spécialisation en machine learning, deep learning et data science',
                'duration_years' => 2,
                'level' => 'master',
                'is_active' => true,
            ],
            [
                'name' => 'Doctorat en Sciences des Données',
                'description' => 'Recherche avancée en big data et analyse prédictive',
                'duration_years' => 3,
                'level' => 'doctorate',
                'is_active' => true,
            ],
            [
                'name' => 'Licence en Gestion des Entreprises',
                'description' => 'Formation en management, marketing et finance',
                'duration_years' => 3,
                'level' => 'bachelor',
                'is_active' => true,
            ],
            [
                'name' => 'Master en Marketing Digital',
                'description' => 'Stratégies digitales, SEO/SEM et social media management',
                'duration_years' => 2,
                'level' => 'master',
                'is_active' => true,
            ],
            [
                'name' => 'Diplôme Universitaire de Technologie (DUT) Informatique',
                'description' => 'Formation technique en développement et administration systèmes',
                'duration_years' => 2,
                'level' => 'technicien',
                'is_active' => true,
            ],
            [
                'name' => 'BTS Comptabilité et Gestion',
                'description' => 'Formation en comptabilité, fiscalité et gestion financière',
                'duration_years' => 2,
                'level' => 'technicien',
                'is_active' => true,
            ],
            [
                'name' => 'Licence en Droit',
                'description' => 'Formation en droit civil, pénal et des affaires',
                'duration_years' => 3,
                'level' => 'bachelor',
                'is_active' => false,
            ],
            [
                'name' => 'Master en Cybersécurité',
                'description' => 'Protection des systèmes d\'information, cryptographie et audit sécurité',
                'duration_years' => 2,
                'level' => 'master',
                'is_active' => true,
            ],
            [
                'name' => 'Doctorat en Intelligence Artificielle',
                'description' => 'Recherche en IA éthique et systèmes autonomes',
                'duration_years' => 3,
                'level' => 'doctorate',
                'is_active' => true,
            ],
        ];

        foreach ($diplomas as $diploma) {
            Diploma::create($diploma);
        }
    }
}