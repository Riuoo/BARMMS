<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Biogesic',
                'generic_name' => 'Paracetamol',
                'category' => 'Pain Relief',
                'description' => 'For the relief of mild to moderate pain and fever.',
                'dosage_form' => 'Tablet 500mg',
                'manufacturer' => 'Unilab',
                'current_stock' => 1200,
                'minimum_stock' => 200,
                'expiry_date' => now()->addMonths(10)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'Amoxicillin',
                'generic_name' => 'Amoxicillin',
                'category' => 'Antibiotic',
                'description' => 'Antibiotic used to treat a number of bacterial infections.',
                'dosage_form' => 'Capsule 500mg',
                'manufacturer' => 'Pfizer',
                'current_stock' => 300,
                'minimum_stock' => 100,
                'expiry_date' => now()->addMonths(6)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'Losartan',
                'generic_name' => 'Losartan Potassium',
                'category' => 'Antihypertensive',
                'description' => 'Used to treat high blood pressure.',
                'dosage_form' => 'Tablet 50mg',
                'manufacturer' => 'MSD',
                'current_stock' => 400,
                'minimum_stock' => 100,
                'expiry_date' => now()->addMonths(9)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'Metformin',
                'generic_name' => 'Metformin Hydrochloride',
                'category' => 'Antidiabetic',
                'description' => 'First-line medication for the treatment of type 2 diabetes.',
                'dosage_form' => 'Tablet 500mg',
                'manufacturer' => 'Torrent',
                'current_stock' => 500,
                'minimum_stock' => 150,
                'expiry_date' => now()->addMonths(12)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'Cetirizine',
                'generic_name' => 'Cetirizine Hydrochloride',
                'category' => 'Antihistamine',
                'description' => 'Used for relief of allergy symptoms such as runny nose and sneezing.',
                'dosage_form' => 'Tablet 10mg',
                'manufacturer' => 'GSK',
                'current_stock' => 250,
                'minimum_stock' => 80,
                'expiry_date' => now()->addMonths(7)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'ORS',
                'generic_name' => 'Oral Rehydration Salts',
                'category' => 'Emergency',
                'description' => 'Used to treat dehydration, especially due to diarrhea.',
                'dosage_form' => 'Sachet',
                'manufacturer' => 'WHO Formulation',
                'current_stock' => 800,
                'minimum_stock' => 200,
                'expiry_date' => now()->addMonths(18)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'Ascorbic Acid',
                'generic_name' => 'Vitamin C',
                'category' => 'Vitamins',
                'description' => 'Vitamin supplement to boost immune system.',
                'dosage_form' => 'Tablet 500mg',
                'manufacturer' => 'Unilab',
                'current_stock' => 1000,
                'minimum_stock' => 300,
                'expiry_date' => now()->addMonths(14)->toDateString(),
                'is_active' => true,
            ],
            [
                'name' => 'Ventolin Nebules',
                'generic_name' => 'Salbutamol',
                'category' => 'Emergency',
                'description' => 'For relief of bronchospasm in asthma.',
                'dosage_form' => 'Nebule 2.5mg/2.5ml',
                'manufacturer' => 'GSK',
                'current_stock' => 180,
                'minimum_stock' => 50,
                'expiry_date' => now()->addMonths(5)->toDateString(),
                'is_active' => true,
            ],
        ];

        foreach ($medicines as $data) {
            Medicine::create($data);
        }
    }
}

