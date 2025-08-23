<?php

namespace App\Http\Controllers;

use App\Http\Requests\content\CreatContentRequest;
use App\Http\Requests\content\UpdateContentRequest;
use App\Http\Resources\ContentResouce;
use App\Services\ContentService;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    protected $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->contentService->getAll();
        return $result['status'] === 200
            ? self::paginated($result['data'], ContentResouce::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatContentRequest $request)
    {
        $data = $request->validated();

        $result = $this->contentService->createContent($data);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $content = $this->contentService->update($id, []);
        return response()->json(['status' => 'success', 'data' => $content]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContentRequest $request, $id)
    {
        $data = $request->validated();



        $result = $this->contentService->updateContent($id, $data);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $result = $this->contentService->deleteContent($id);

        return $result['status'] === 200
            ? self::success($result['data'], $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
