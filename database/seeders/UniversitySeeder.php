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

        foreach ($data as $item) {
            // حفظ المحافظة
            $governorate = Governorate::firstOrCreate(['name' => $item['governorate']]);

            // حفظ الجامعة
            $university = University::firstOrCreate([
                'name' => $item['universityName'],
                'governorate_id' => $governorate->id
            ]);

            // التعامل مع القسم الرئيسي
            $departmentId = null;
            if (!empty($item['departments'])) {
                // نأخذ القسم الأول فقط كقسم رئيسي للكلية
                $department = Department::firstOrCreate(['name' => $item['departments'][0]]);
                $departmentId = $department->id;
            }

            // حفظ الكلية وربطها بالقسم الرئيسي
            $college = College::create([
                'name' => $item['collegeName'],
                'college_type' => $item['collegeType'],
                'study_duration' => $item['studyDuration'],
                'university_id' => $university->id,
                'department_id' => $departmentId
            ]);

            // إنشاء باقي الأقسام إذا موجودة
            foreach ($item['departments'] as $dep) {
                $depModel = Department::firstOrCreate(['name' => $dep]);
            }

            // حفظ البرانش (تطبيقي / أحيائي / علمي)
            foreach ($item['admissions'] as $adm) {
                $branchName = $item['branch'];

                // تعديل 2025 إلى علمي
                if ($adm['year'] == 2025 && in_array($branchName, ['تطبيقي', 'احيائي'])) {
                    $branchName = 'علمي';
                }

                $branch = Branch::firstOrCreate(['name' => $branchName]);

                // حفظ بيانات القبول
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
