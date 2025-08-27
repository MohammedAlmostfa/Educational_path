<?php

namespace App\Http\Controllers;

use App\Http\Requests\content\CreatContentRequest;
use App\Http\Requests\content\FilterContent;
use App\Http\Requests\content\UpdateContentRequest;
use App\Http\Resources\ContentResouce;
use App\Services\ContentService;
use Illuminate\Http\Request;

/**
 * Class ContentController
 *
 * Controller responsible for managing content resources.
 * Handles CRUD operations: listing, creating, viewing, updating, and deleting content.
 */
class ContentController extends Controller
{
    /**
     * @var ContentService
     * Service instance for handling content-related operations
     */
    protected $contentService;

    /**
     * Constructor to inject ContentService
     *
     * @param ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Display a listing of the content resources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FilterContent $request)
    {    $data = $request->validated();
        // Fetch all content using the service
        $result = $this->contentService->getAll($data);

        // Return paginated response if successful, otherwise error
        return $result['status'] === 200
            ? self::paginated($result['data'], ContentResouce::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Store a newly created content resource in storage.
     *
     * @param CreatContentRequest $request Validated request data for creating content
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatContentRequest $request)
    {
        // Validate incoming request data
        $data = $request->validated();

        // Create new content using the service
        $result = $this->contentService->createContent($data);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified content resource in storage.
     *
     * @param UpdateContentRequest $request Validated request data for update
     * @param int $id Content ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateContentRequest $request, $id)
    {
        // Validate incoming request data
        $data = $request->validated();

        // Update content using the service
        $result = $this->contentService->updateContent($id, $data);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified content resource from storage.
     *
     * @param int $id Content ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Delete content using the service
        $result = $this->contentService->deleteContent($id);

        // Return success or error response
        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

       public function addViewers($id)
{
        $result = $this->contentService->addViewers($id);


        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
