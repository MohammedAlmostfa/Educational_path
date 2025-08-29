<?php

namespace App\Services;

use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\SendFcmNotificationJob;
use App\Models\DeviceToken;

/**
 * Class ContentService
 *
 * Service responsible for managing content:
 * - Retrieve all contents
 * - Create new content
 * - Update existing content
 * - Delete content
 *
 * Handles image uploads and sends FCM notifications for new content.
 */
class ContentService extends Service
{
    /**
     * Get all contents with optional filtering and pagination.
     *
     * @param array $filteringData Optional filtering parameters
     * @return array Response with paginated content and Arabic message
     */
    public function getAll($filteringData)
    {
        try {
            $content = Content::when(!empty($filteringData), fn($query) => $query->filterBy($filteringData))
                ->orderByDesc('created_at')
                ->paginate(10);

            return $this->successResponse('تم جلب المحتوى بنجاح.', 200, $content);
        } catch (Exception $e) {
            Log::error('Error fetching contents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new content.
     *
     * Handles optional image upload and dispatches FCM notification to all device tokens.
     *
     * @param array $data Content data (title, body, optional image, is_new flag)
     * @return array Response with created content and Arabic message
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

            // Handle image upload if provided
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

            // Create content in DB
            $content = Content::create($contentData);
            DB::commit();

            // Send FCM notification to all users
            $tokens = DeviceToken::whereNotNull('user_id')
                ->pluck('fcm_token')
                ->filter()
                ->toArray();

            if (!empty($tokens)) {
                SendFcmNotificationJob::dispatch($content->title, $content->body, $tokens);
            }

            return $this->successResponse('تم إنشاء المحتوى بنجاح.', 200, $content);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating content: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء المحتوى. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Update an existing content by ID.
     *
     * Handles optional image replacement and retains old data if fields are missing.
     *
     * @param int $id Content ID
     * @param array $data Content data (title, body, optional image, is_new flag)
     * @return array Response with updated content and Arabic message
     */
    public function updateContent(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $content = Content::findOrFail($id);

            $content->title  = $data['title'] ?? $content->title;
            $content->body   = $data['body'] ?? $content->body;
            $content->is_new = $data['is_new'] ?? $content->is_new;

            // Replace image if new image is provided
            if (!empty($data['image'])) {
                if (!empty($content->image_url)) {
                    $oldImagePath = public_path($content->image_url);
                    if (is_file($oldImagePath) && file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $folder = 'content/' . date('Y-m-d');
                $fullPath = public_path($folder);
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                $imageName = Str::random(32) . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move($fullPath, $imageName);
                $content->image_url = $folder . '/' . $imageName;
            }

            $content->save();
            DB::commit();

            return $this->successResponse('تم تحديث المحتوى بنجاح.', 200, $content);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating content: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث المحتوى. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Delete a content by ID.
     *
     * Removes associated image file if exists.
     *
     * @param int $id Content ID
     * @return array Response with success or error message in Arabic
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
            return $this->errorResponse('حدث خطأ أثناء حذف المحتوى. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    /**
     * Increment the viewers count for a specific content.
     *
     * @param int $id Content ID
     * @return array Response with success or error message in Arabic
     */
    public function addViewers(int $id)
    {
        try {
            $content = Content::findOrFail($id);

            if ($content) {
                $content->viewers += 1;
                $content->save();
            }

            return $this->successResponse('تم تحديث عدد مشاهدات المحتوى بنجاح.', 200);
        } catch (Exception $e) {
            Log::error('Error updating content viewers: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء تحديث عدد المشاهدات. يرجى المحاولة مرة أخرى.', 500);
        }
    }
}
