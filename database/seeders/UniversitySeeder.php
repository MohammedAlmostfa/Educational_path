<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\College;
use App\Models\Department;
use App\Models\Governorate;
use App\Models\University;
use App\Models\CollegeType; // ✅ استدعاء الموديل الصحيح
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

            // الفرع
            $branchName = $item['branch'] ?? 'عام';
            if (in_array($branchName, ['تطبيقي', 'احيائي'])) {
                $branchName = 'علمي';
            }
            $branch = Branch::firstOrCreate(['name' => $branchName]);

            // ✅ نوع الكلية (باستخدام CollegeType بدلاً من Department)
            $collegeType = CollegeType::firstOrCreate(
                ['name' => $item['collegeType']]
            );

            // الكلية
            $college = College::firstOrCreate(
                [
                    'name'          => $item['collegeName'],
                    'university_id' => $university->id,
                ],
                [
                    'college_type_id' => $collegeType->id,
                    'study_duration'  => $item['studyDuration'],
                    'branch_id'       => $branch->id,
                    'gender'          => $genderMap[$item['gender']] ?? 2,
                ]
            );

            // الأقسام
            if (!empty($item['departments'])) {
                $priority = 0;
                foreach ($item['departments'] as $dep) {
                    $department = Department::firstOrCreate(
                        ['name' => $dep],

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
                    // تجاهل admission إذا كل الثلاثة حقول null
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
