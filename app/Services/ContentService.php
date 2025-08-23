<?php

namespace App\Services;

use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
  use App\Jobs\SendFcmNotificationJob;
use App\Models\DeviceToken;
class ContentService extends Service
{
    /**
     * Get all contents.
     */
    public function getAll()
    {
        try {
            $content = Content::paginate(10);
            return $this->successResponse('تم إنشاء المحتوى بنجاح.', 200, $content);
        } catch (Exception $e) {
            Log::error('Error fetching contents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new content.
     */
 

public function createContent(array $data)
{
    DB::beginTransaction();

    try {
        $contentData = [
            'title'  => $data['title'],
            'body'   => $data['body'],
            'is_new' => $data['is_new'] ?? 1,
        ];

        if (!empty($data['image'])) {
            $photo = $data['image'];
            $folder = 'content/' . date('Y-m-d');
            $fullPath = public_path($folder);

            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            $imageName = Str::random(32) . '.' . $photo->getClientOriginalExtension();
            $photo->move($fullPath, $imageName);
            $contentData['image_url'] = $folder . '/' . $imageName;
        }

        $content = Content::create($contentData);

        DB::commit();

        // 🔥 إرسال الإشعار في الخلفية
        $tokens = DeviceToken::pluck('fcm_token')->filter()->toArray();
        if (!empty($tokens)) {
            SendFcmNotificationJob::dispatch($content->title, $content->body, $tokens);
        }

        return $this->successResponse('تم إنشاء المحتوى بنجاح.', 200, $content);

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error creating content: ' . $e->getMessage());

        return $this->errorResponse('حدث خطأ أثناء إنشاء المحتوى، يرجى المحاولة مرة أخرى.', 500);
    }
}


    public function updateContent(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $content = Content::findOrFail($id);

            // تحديث النصوص
            $content->title  = $data['title'] ?? $content->title;
            $content->body   = $data['body'] ?? $content->body;
            $content->is_new = $data['is_new'] ?? $content->is_new;

            // تحديث الصورة إذا تم إرسال واحدة جديدة
            if (!empty($data['image'])) {
                // حذف الصورة القديمة إذا كانت موجودة
                if (!empty($content->image_url)) {
                    $oldImagePath = public_path($content->image_url);
                    if (is_file($oldImagePath) && file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // إنشاء مجلد باسم تاريخ اليوم إذا لم يكن موجودًا
                $folder = 'content/' . date('Y-m-d');
                $fullPath = public_path($folder);
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                // اسم الصورة عشوائي مع امتداد الصورة
                $imageName = Str::random(32) . '.' . $data['image']->getClientOriginalExtension();

                // نقل الصورة الجديدة إلى المجلد
                $data['image']->move($fullPath, $imageName);

                // تحديث المسار في قاعدة البيانات
                $content->image_url = $folder . '/' . $imageName;
            }

            $content->save();
            DB::commit();

            return $this->successResponse('تم تحديث المحتوى بنجاح.', 200, $content);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating content: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث المحتوى، يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Delete a content by ID.
     */
    public function deleteContent(int $id)
    {
        DB::beginTransaction();

        try {
            $content = Content::findOrFail($id);

            if (!empty($content->image_url)) {
                $imagePath = public_path($content->image_url);
                if ($imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $content->delete();

            DB::commit();

            return $this->successResponse('تم حذف المحتوى بنجاح.', 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting content: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف المحتوى، يرجى المحاولة مرة أخرى.', 500);
        }
    }
}
