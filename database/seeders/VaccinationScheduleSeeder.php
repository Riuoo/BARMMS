<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaccinationSchedule;

class VaccinationScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Infant Vaccinations (0-12 months)
        $this->createInfantSchedules();
        
        // Toddler Vaccinations (12-36 months)
        $this->createToddlerSchedules();
        
        // Child Vaccinations (3-12 years)
        $this->createChildSchedules();
        
        // Adolescent Vaccinations (12-18 years)
        $this->createAdolescentSchedules();
        
        // Adult Vaccinations (18+ years)
        $this->createAdultSchedules();
        
        // Elderly Vaccinations (65+ years)
        $this->createElderlySchedules();
    }

    private function createInfantSchedules()
    {
        // Hepatitis B - Birth dose
        VaccinationSchedule::create([
            'vaccine_name' => 'Hepatitis B',
            'vaccine_type' => 'Hepatitis B',
            'age_group' => 'Infant',
            'age_min_months' => 0,
            'age_max_months' => 1,
            'dose_number' => 1,
            'total_doses_required' => 3,
            'interval_months' => 1,
            'description' => 'First dose of Hepatitis B vaccine series',
            'is_active' => true,
        ]);

        // DTaP - 2 months
        VaccinationSchedule::create([
            'vaccine_name' => 'DTaP (Diphtheria, Tetanus, Pertussis)',
            'vaccine_type' => 'DTaP',
            'age_group' => 'Infant',
            'age_min_months' => 2,
            'age_max_months' => 2,
            'dose_number' => 1,
            'total_doses_required' => 5,
            'interval_months' => 2,
            'description' => 'First dose of DTaP vaccine series',
            'is_active' => true,
        ]);

        // Rotavirus - 2 months
        VaccinationSchedule::create([
            'vaccine_name' => 'Rotavirus',
            'vaccine_type' => 'Rotavirus',
            'age_group' => 'Infant',
            'age_min_months' => 2,
            'age_max_months' => 2,
            'dose_number' => 1,
            'total_doses_required' => 3,
            'interval_months' => 2,
            'description' => 'First dose of Rotavirus vaccine series',
            'is_active' => true,
        ]);

        // Pneumococcal - 2 months
        VaccinationSchedule::create([
            'vaccine_name' => 'Pneumococcal Conjugate',
            'vaccine_type' => 'Pneumococcal',
            'age_group' => 'Infant',
            'age_min_months' => 2,
            'age_max_months' => 2,
            'dose_number' => 1,
            'total_doses_required' => 4,
            'interval_months' => 2,
            'description' => 'First dose of Pneumococcal vaccine series',
            'is_active' => true,
        ]);

        // Hib - 2 months
        VaccinationSchedule::create([
            'vaccine_name' => 'Haemophilus influenzae type b (Hib)',
            'vaccine_type' => 'Hib',
            'age_group' => 'Infant',
            'age_min_months' => 2,
            'age_max_months' => 2,
            'dose_number' => 1,
            'total_doses_required' => 4,
            'interval_months' => 2,
            'description' => 'First dose of Hib vaccine series',
            'is_active' => true,
        ]);

        // IPV - 2 months
        VaccinationSchedule::create([
            'vaccine_name' => 'IPV (Inactivated Poliovirus)',
            'vaccine_type' => 'Other',
            'age_group' => 'Infant',
            'age_min_months' => 2,
            'age_max_months' => 2,
            'dose_number' => 1,
            'total_doses_required' => 4,
            'interval_months' => 2,
            'description' => 'First dose of IPV vaccine series',
            'is_active' => true,
        ]);
    }

    private function createToddlerSchedules()
    {
        // MMR - 12 months
        VaccinationSchedule::create([
            'vaccine_name' => 'MMR (Measles, Mumps, Rubella)',
            'vaccine_type' => 'MMR',
            'age_group' => 'Toddler',
            'age_min_months' => 12,
            'age_max_months' => 15,
            'dose_number' => 1,
            'total_doses_required' => 2,
            'interval_years' => 4,
            'description' => 'First dose of MMR vaccine series',
            'is_active' => true,
        ]);

        // Varicella - 12 months
        VaccinationSchedule::create([
            'vaccine_name' => 'Varicella (Chickenpox)',
            'vaccine_type' => 'Varicella',
            'age_group' => 'Toddler',
            'age_min_months' => 12,
            'age_max_months' => 15,
            'dose_number' => 1,
            'total_doses_required' => 2,
            'interval_years' => 3,
            'description' => 'First dose of Varicella vaccine series',
            'is_active' => true,
        ]);
    }

    private function createChildSchedules()
    {
        // DTaP Booster - 4-6 years
        VaccinationSchedule::create([
            'vaccine_name' => 'DTaP Booster',
            'vaccine_type' => 'DTaP',
            'age_group' => 'Child',
            'age_min_years' => 4,
            'age_max_years' => 6,
            'dose_number' => 5,
            'total_doses_required' => 5,
            'is_booster' => true,
            'description' => 'Final dose of DTaP vaccine series',
            'is_active' => true,
        ]);

        // IPV Booster - 4-6 years
        VaccinationSchedule::create([
            'vaccine_name' => 'IPV Booster',
            'vaccine_type' => 'Other',
            'age_group' => 'Child',
            'age_min_years' => 4,
            'age_max_years' => 6,
            'dose_number' => 4,
            'total_doses_required' => 4,
            'is_booster' => true,
            'description' => 'Final dose of IPV vaccine series',
            'is_active' => true,
        ]);
    }

    private function createAdolescentSchedules()
    {
        // Tdap - 11-12 years
        VaccinationSchedule::create([
            'vaccine_name' => 'Tdap (Tetanus, Diphtheria, Pertussis)',
            'vaccine_type' => 'Tetanus',
            'age_group' => 'Adolescent',
            'age_min_years' => 11,
            'age_max_years' => 12,
            'dose_number' => 1,
            'total_doses_required' => 1,
            'description' => 'Tdap booster for adolescents',
            'is_active' => true,
        ]);

        // HPV - 11-12 years
        VaccinationSchedule::create([
            'vaccine_name' => 'HPV (Human Papillomavirus)',
            'vaccine_type' => 'HPV',
            'age_group' => 'Adolescent',
            'age_min_years' => 11,
            'age_max_years' => 12,
            'dose_number' => 1,
            'total_doses_required' => 2,
            'interval_months' => 6,
            'description' => 'First dose of HPV vaccine series',
            'is_active' => true,
        ]);

        // Meningococcal - 11-12 years
        VaccinationSchedule::create([
            'vaccine_name' => 'Meningococcal ACWY',
            'vaccine_type' => 'Other',
            'age_group' => 'Adolescent',
            'age_min_years' => 11,
            'age_max_years' => 12,
            'dose_number' => 1,
            'total_doses_required' => 2,
            'interval_years' => 5,
            'description' => 'First dose of Meningococcal vaccine series',
            'is_active' => true,
        ]);
    }

    private function createAdultSchedules()
    {
        // Td Booster - Every 10 years
        VaccinationSchedule::create([
            'vaccine_name' => 'Td (Tetanus, Diphtheria)',
            'vaccine_type' => 'Tetanus',
            'age_group' => 'Adult',
            'age_min_years' => 18,
            'age_max_years' => 64,
            'dose_number' => 1,
            'total_doses_required' => 1,
            'interval_years' => 10,
            'is_booster' => true,
            'description' => 'Td booster every 10 years',
            'is_active' => true,
        ]);

        // Influenza - Annual
        VaccinationSchedule::create([
            'vaccine_name' => 'Influenza',
            'vaccine_type' => 'Influenza',
            'age_group' => 'Adult',
            'age_min_years' => 18,
            'age_max_years' => 64,
            'dose_number' => 1,
            'total_doses_required' => 1,
            'is_annual' => true,
            'description' => 'Annual influenza vaccination',
            'is_active' => true,
        ]);
    }

    private function createElderlySchedules()
    {
        // Pneumococcal - 65+ years
        VaccinationSchedule::create([
            'vaccine_name' => 'Pneumococcal (PCV13)',
            'vaccine_type' => 'Pneumonia',
            'age_group' => 'Elderly',
            'age_min_years' => 65,
            'age_max_years' => 120,
            'dose_number' => 1,
            'total_doses_required' => 1,
            'description' => 'Pneumococcal vaccination for elderly',
            'is_active' => true,
        ]);

        // Shingles - 50+ years
        VaccinationSchedule::create([
            'vaccine_name' => 'Shingles (Zoster)',
            'vaccine_type' => 'Other',
            'age_group' => 'Elderly',
            'age_min_years' => 50,
            'age_max_years' => 120,
            'dose_number' => 1,
            'total_doses_required' => 2,
            'interval_months' => 2,
            'description' => 'Shingles vaccine series',
            'is_active' => true,
        ]);
    }
}
