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
            'اناث'   => 0,
            'ذكور'    => 1,
            'كلاهما' => 2,
        ];

        foreach ($data as $item) {
            // حفظ المحافظة
            $governorate = Governorate::firstOrCreate([
                'name' => $item['governorate']
            ]);

            // حفظ الجامعة
            $university = University::firstOrCreate([
                'name'           => $item['universityName'],
                'governorate_id' => $governorate->id,
            ]);

            // جلب أو إنشاء الفرع (لكل كلية)
            $branchName = $item['branch'] ?? 'عام';
            if (in_array($branchName, ['تطبيقي', 'احيائي'])) {
                $branchName = 'علمي';
            }
            $branch = Branch::firstOrCreate(['name' => $branchName]);

            // جلب أو إنشاء "نوع الكلية" في جدول departments مع type = 1
            $collegeType = Department::firstOrCreate(
                ['name' => $item['collegeType']], // الاسم
                ['type' => 1]                     // type = 1 يعني نوع كلية
            );

            // إنشاء الكلية وربطها بالـ "نوع" المخزن في departments
            $college = College::create([
                'name'             => $item['collegeName'],
                'college_type_id'  => $collegeType->id,
                'study_duration'   => $item['studyDuration'],
                'university_id'    => $university->id,
                'branch_id'        => $branch->id,
                'gender'           => $genderMap[$item['gender']] ?? 2,
            ]);

            // ربط الأقسام بالكلية عبر pivot
            if (!empty($item['departments'])) {
                $priority = 0;
                foreach ($item['departments'] as $dep) {
                    // تخزين القسم العادي مع type = 0
                    $department = Department::firstOrCreate(
                        ['name' => $dep],
                        ['type' => 0] // type = 0 يعني قسم عادي
                    );

                    $college->departments()->syncWithoutDetaching([
                        $department->id => ['priority' => $priority],
                    ]);
                    $priority++;
                }
            }

            // حفظ بيانات القبول
            if (!empty($item['admissions'])) {
                foreach ($item['admissions'] as $adm) {
                    Admission::create([
                        'college_id'       => $college->id,
                        'year'             => $adm['year'],
                        'min_average'      => $adm['minAverage'],
                        'min_total'        => $adm['minTotal'],
                        'preference_score' => $adm['preferenceScore'],
                    ]);
                }
            }
        }
    }
}
