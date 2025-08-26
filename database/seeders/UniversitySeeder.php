<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\College;
use App\Models\Department;
use App\Models\Governorate;
use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/universities.json'));
        $data = json_decode($json, true);

        // خريطة تحويل الجنس من نص إلى رقم
        $genderMap = [
            'أنثى'   => 0,
            'ذكر'    => 1,
            'كلاهما' => 2,
        ];

        foreach ($data as $item) {
            // حفظ المحافظة
            $governorate = Governorate::firstOrCreate(['name' => $item['governorate']]);

            // حفظ الجامعة
            $university = University::firstOrCreate([
                'name'          => $item['universityName'],
                'governorate_id'=> $governorate->id,
            ]);

            // حفظ الكلية
            $college = College::create([
                'name'           => $item['collegeName'],
                'college_type'   => $item['collegeType'],
                'study_duration' => $item['studyDuration'],
                'university_id'  => $university->id,
                'gender'         => $genderMap[$item['gender']] ?? 2, // الافتراضي = 2 (كلاهما)
            ]);

            // ربط الأقسام بالكلية عبر جدول pivot
            if (!empty($item['departments'])) {
                $priority = 0;
                foreach ($item['departments'] as $dep) {
                    $department = Department::firstOrCreate(['name' => $dep]);

                    // syncWithoutDetaching حتى ما يكرر الربط
                    $college->departments()->syncWithoutDetaching([
                        $department->id => ['priority' => $priority],
                    ]);
                    $priority++;
                }
            }

            // حفظ بيانات القبول حسب الفروع والسنوات
            foreach ($item['admissions'] as $adm) {
                $branchName = $item['branch'];

                // تعديل 2025 إلى علمي إذا كان تطبيقي أو احيائي
                if ( in_array($branchName, ['تطبيقي', 'احيائي'])) {
                    $branchName = 'علمي';
                }

                $branch = Branch::firstOrCreate(['name' => $branchName]);

                Admission::create([
                    'college_id'       => $college->id,
                    'branch_id'        => $branch->id,
                    'year'             => $adm['year'],
                    'min_average'      => $adm['minAverage'],
                    'min_total'        => $adm['minTotal'],
                    'preference_score' => $adm['preferenceScore'],
                ]);
            }
        }
    }
}
