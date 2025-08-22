<?php

namespace App\Services;

use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Log;

class ContentService
{
    /**
     * Get all contents.
     */
    public function getAll()
    {
        try {
            return Content::all();
        } catch (Exception $e) {
            Log::error('Error fetching contents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new content.
     */
    public function create(array $data)
    {
        try {
            return Content::create($data);
        } catch (Exception $e) {
            Log::error('Error creating content: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a content by ID.
     */
    public function update(int $id, array $data)
    {
        try {
            $content = Content::findOrFail($id);
            $content->update($data);
            return $content;
        } catch (Exception $e) {
            Log::error('Error updating content: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a content by ID.
     */
    public function delete(int $id)
    {
        try {
            $content = Content::findOrFail($id);
            $content->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting content: ' . $e->getMessage());
            return false;
        }
    }
}
