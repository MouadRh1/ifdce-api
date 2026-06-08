<?php

namespace Database\Seeders;

use App\Models\Diploma;
use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les diplômes existants
        $licenceInfo = Diploma::where('name', 'Licence en Informatique')->first();
        $masterIA = Diploma::where('name', 'Master en Intelligence Artificielle')->first();
        $doctoratData = Diploma::where('name', 'Doctorat en Sciences des Données')->first();
        $licenceGestion = Diploma::where('name', 'Licence en Gestion des Entreprises')->first();
        $masterMarketing = Diploma::where('name', 'Master en Marketing Digital')->first();
        $dutInfo = Diploma::where('name', 'Diplôme Universitaire de Technologie (DUT) Informatique')->first();
        $btsCompta = Diploma::where('name', 'BTS Comptabilité et Gestion')->first();
        $licenceDroit = Diploma::where('name', 'Licence en Droit')->first();
        $masterCyber = Diploma::where('name', 'Master en Cybersécurité')->first();
        $doctoratIA = Diploma::where('name', 'Doctorat en Intelligence Artificielle')->first();

        $fields = [
            // Filières pour Licence en Informatique
            [
                'diploma_id' => $licenceInfo?->id,
                'name' => 'Génie Logiciel',
                'description' => 'Développement d\'applications, conception et gestion de projets logiciels',
                'max_students' => 60,
                'is_active' => true,
            ],
            [
                'diploma_id' => $licenceInfo?->id,
                'name' => 'Systèmes et Réseaux',
                'description' => 'Administration systèmes, réseaux et sécurité informatique',
                'max_students' => 45,
                'is_active' => true,
            ],
            [
                'diploma_id' => $licenceInfo?->id,
                'name' => 'Bases de Données',
                'description' => 'Conception, administration et optimisation des bases de données',
                'max_students' => 40,
                'is_active' => true,
            ],

            // Filières pour Master en Intelligence Artificielle
            [
                'diploma_id' => $masterIA?->id,
                'name' => 'Machine Learning',
                'description' => 'Algorithmes d\'apprentissage automatique et statistiques avancées',
                'max_students' => 35,
                'is_active' => true,
            ],
            [
                'diploma_id' => $masterIA?->id,
                'name' => 'Deep Learning',
                'description' => 'Réseaux de neurones profonds et architectures avancées',
                'max_students' => 30,
                'is_active' => true,
            ],
            [
                'diploma_id' => $masterIA?->id,
                'name' => 'Vision par Ordinateur',
                'description' => 'Traitement d\'images, reconnaissance faciale et objets',
                'max_students' => 25,
                'is_active' => false,
            ],

            // Filières pour Doctorat en Sciences des Données
            [
                'diploma_id' => $doctoratData?->id,
                'name' => 'Big Data Analytics',
                'description' => 'Analyse de données massives et architectures distribuées',
                'max_students' => 15,
                'is_active' => true,
            ],
            [
                'diploma_id' => $doctoratData?->id,
                'name' => 'Data Mining',
                'description' => 'Extraction de connaissances et fouille de données',
                'max_students' => 12,
                'is_active' => true,
            ],

            // Filières pour Licence en Gestion des Entreprises
            [
                'diploma_id' => $licenceGestion?->id,
                'name' => 'Finance d\'Entreprise',
                'description' => 'Gestion financière, analyse comptable et contrôle de gestion',
                'max_students' => 50,
                'is_active' => true,
            ],
            [
                'diploma_id' => $licenceGestion?->id,
                'name' => 'Marketing et Commerce',
                'description' => 'Stratégies marketing, comportement du consommateur et vente',
                'max_students' => 55,
                'is_active' => true,
            ],
            [
                'diploma_id' => $licenceGestion?->id,
                'name' => 'Ressources Humaines',
                'description' => 'Gestion des talents, recrutement et droit du travail',
                'max_students' => 45,
                'is_active' => true,
            ],

            // Filières pour Master en Marketing Digital
            [
                'diploma_id' => $masterMarketing?->id,
                'name' => 'Social Media Management',
                'description' => 'Gestion des réseaux sociaux, community management et influence',
                'max_students' => 30,
                'is_active' => true,
            ],
            [
                'diploma_id' => $masterMarketing?->id,
                'name' => 'SEO/SEM',
                'description' => 'Optimisation pour moteurs de recherche et publicité en ligne',
                'max_students' => 28,
                'is_active' => true,
            ],
            [
                'diploma_id' => $masterMarketing?->id,
                'name' => 'E-commerce',
                'description' => 'Boutiques en ligne, conversion et expérience utilisateur',
                'max_students' => 32,
                'is_active' => false,
            ],

            // Filières pour DUT Informatique
            [
                'diploma_id' => $dutInfo?->id,
                'name' => 'Développement Web',
                'description' => 'Création de sites et applications web front-end/back-end',
                'max_students' => 40,
                'is_active' => true,
            ],
            [
                'diploma_id' => $dutInfo?->id,
                'name' => 'Administration Systèmes',
                'description' => 'Gestion des serveurs Linux/Windows et virtualisation',
                'max_students' => 35,
                'is_active' => true,
            ],

            // Filières pour BTS Comptabilité et Gestion
            [
                'diploma_id' => $btsCompta?->id,
                'name' => 'Comptabilité Générale',
                'description' => 'Tenue de comptabilité, bilan et compte de résultat',
                'max_students' => 45,
                'is_active' => true,
            ],
            [
                'diploma_id' => $btsCompta?->id,
                'name' => 'Gestion Fiscale',
                'description' => 'Déclarations fiscales, TVA et impôts',
                'max_students' => 40,
                'is_active' => true,
            ],

            // Filières pour Licence en Droit
            [
                'diploma_id' => $licenceDroit?->id,
                'name' => 'Droit des Affaires',
                'description' => 'Droit commercial, sociétés et contrats',
                'max_students' => 50,
                'is_active' => false,
            ],

            // Filières pour Master en Cybersécurité
            [
                'diploma_id' => $masterCyber?->id,
                'name' => 'Sécurité Offensive',
                'description' => 'Tests d\'intrusion, pentesting et ethical hacking',
                'max_students' => 25,
                'is_active' => true,
            ],
            [
                'diploma_id' => $masterCyber?->id,
                'name' => 'Sécurité Défensive',
                'description' => 'Détection d\'intrusion, SOC et analyse malware',
                'max_students' => 28,
                'is_active' => true,
            ],
            [
                'diploma_id' => $masterCyber?->id,
                'name' => 'Cryptographie',
                'description' => 'Algorithmes cryptographiques, PKI et blockchain',
                'max_students' => 20,
                'is_active' => true,
            ],

            // Filières pour Doctorat en Intelligence Artificielle
            [
                'diploma_id' => $doctoratIA?->id,
                'name' => 'IA Éthique',
                'description' => 'Algorithmes responsables, biais et transparence',
                'max_students' => 10,
                'is_active' => true,
            ],
            [
                'diploma_id' => $doctoratIA?->id,
                'name' => 'Systèmes Autonomes',
                'description' => 'Robots intelligents, véhicules autonomes et agents',
                'max_students' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($fields as $field) {
            if ($field['diploma_id']) {
                Field::create($field);
            }
        }
    }
}