<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\College;
use App\Models\Department;
use App\Models\Governorate;
use App\Models\University;
use App\Models\CollegeType;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/universities.json'));
        $data = json_decode($json, true);

        // تحويل الجنس من نص إلى رقم
        $genderMap = [
            'اناث'   => 0,
            'ذكور'   => 1,
            'كلاهما' => 2,
        ];

        foreach ($data as $item) {
            // المحافظة
            $governorate = Governorate::firstOrCreate([
                'name' => $item['governorate'],
            ]);

            // الجامعة
            $university = University::firstOrCreate([
                'name'           => $item['universityName'],
                'governorate_id' => $governorate->id,
            ]);

            // الفرع (تحقق قبل الإنشاء)
            $branchName = $item['branch'] ?? 'عام';
            if (in_array($branchName, ['تطبيقي', 'احيائي'])) {
                $branchName = 'علمي';
            }
            $branch = Branch::firstOrCreate(['name' => $branchName]);

            // نوع الكلية
            $collegeType = CollegeType::firstOrCreate(['name' => $item['collegeType']]);

            // الكلية
            $college = College::firstOrCreate([
                'name'           => $item['collegeName'],
                'university_id'  => $university->id,
                'branch_id'      => $branch->id,
                'college_type_id' => $collegeType->id,
                'study_duration' => $item['studyDuration'],
                'gender'         => $genderMap[$item['gender']] ?? 2,
            ]);


            // الأقسام
            if (!empty($item['departments'])) {
                $priority = 0;
                foreach ($item['departments'] as $dep) {
                    $department = Department::firstOrCreate(
                        ['name' => $dep],
                        ['type' => 0]
                    );

                    $college->departments()->syncWithoutDetaching([
                        $department->id => ['priority' => $priority],
                    ]);
                    $priority++;
                }
            }

            // بيانات القبول (admissions)
            if (!empty($item['admissions'])) {
                foreach ($item['admissions'] as $adm) {
                    if (
                        ($adm['minAverage'] ?? null) === null &&
                        ($adm['minTotal'] ?? null) === null &&
                        ($adm['preferenceScore'] ?? null) === null
                    ) {
                        continue;
                    }

                    Admission::create([
                        'college_id'       => $college->id,
                        'year'             => $adm['year'] ?? null,
                        'min_average'      => $adm['minAverage'] ?? null,
                        'min_total'        => $adm['minTotal'] ?? null,
                        'preference_score' => $adm['preferenceScore'] ?? null,
                    ]);
                }
            }
        }
    }
}
