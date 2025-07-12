<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\TertiaryTopicRequest as StoreRequest;
use App\Models\TertiaryTopic;
use App\Repositories\Backend\TertiaryTopicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TertiaryTopicController extends Controller
{
    protected $tertiaryTopicRepo;

    public function __construct()
    {
        $this->tertiaryTopicRepo = app(TertiaryTopicRepository::class);
    }

    public function index(): JsonResponse
    {
        $tertiaryTopics = $this->tertiaryTopicRepo->getAll();

        return response()->json([
            'data' => $tertiaryTopics
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $tertiaryTopic = $this->tertiaryTopicRepo->store($request->validated());

        return response()->json([
            'message' => 'Tertiary Topic created successfully',
            'data' => $tertiaryTopic
        ], Response::HTTP_CREATED);
    }

    public function show(string $uid): JsonResponse
    {
        $tertiaryTopic = $this->tertiaryTopicRepo->findByUid($uid);

        if (!$tertiaryTopic) {
            return response()->json([
                'message' => 'Tertiary Topic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $tertiaryTopic
        ]);
    }

    public function update(StoreRequest $request, TertiaryTopic $tertiaryTopic): JsonResponse
    {
        $tertiaryTopic = $this->tertiaryTopicRepo->update($tertiaryTopic, $request->validated());

        return response()->json([
            'message' => 'Tertiary Topic updated successfully',
            'data' => $tertiaryTopic
        ]);
    }

    public function destroy(TertiaryTopic $tertiaryTopic): JsonResponse
    {
        $deleted = $this->tertiaryTopicRepo->delete($tertiaryTopic);

        if (!$deleted) {
            return response()->json([
                'message' => 'Tertiary Topic could not be deleted'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Tertiary Topic deleted successfully'
        ]);
    }

    public function getBySubtopic($subtopicId): JsonResponse
    {
        $tertiaryTopics = $this->tertiaryTopicRepo->getBySubtopicId($subtopicId);
        return response()->json([
            'data' => $tertiaryTopics
        ]);
    }
}
