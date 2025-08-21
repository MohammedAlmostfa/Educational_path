<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Governorate;
use App\Models\University;
use App\Models\College;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Admission;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/universities.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            // حفظ المحافظة
            $governorate = Governorate::firstOrCreate(['name' => $item['governorate']]);

            // حفظ الجامعة
            $university = University::firstOrCreate([
                'name' => $item['universityName'],
                'governorate_id' => $governorate->id
            ]);

            // حفظ الكلية
            $college = College::create([
                'name' => $item['collegeName'],
                'college_type' => $item['collegeType'],
                'study_duration' => $item['studyDuration'],
                'university_id' => $university->id
            ]);

            // حفظ الأقسام
            foreach ($item['departments'] as $dep) {
                Department::create([
                    'name' => $dep,
                    'college_id' => $college->id
                ]);
            }

            // حفظ الفرع (تطبيقي / أحيائي)
            $branch = Branch::firstOrCreate(['name' => $item['branch']]);

            // حفظ بيانات القبول
            foreach ($item['admissions'] as $adm) {
                Admission::create([
                    'college_id' => $college->id,
                    'branch_id' => $branch->id,
                    'year' => $adm['year'],
                    'min_average' => $adm['minAverage'],
                    'min_total' => $adm['minTotal'],
                    'preference_score' => $adm['preferenceScore']
                ]);
            }
        }
    }
}
