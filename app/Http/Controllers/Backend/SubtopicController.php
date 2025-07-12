<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSubtopicRequest;
use App\Models\Access\Client;
use App\Models\SubTopic;
use App\Repositories\Backend\SubTopicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SubtopicController extends Controller
{
    protected $subtopicRepo;

    public function __construct()
    {
        $this->subtopicRepo = app(SubtopicRepository::class);
    }

    public function index()
    {
        return view('pages.backend.topic.subtopic.index');
    }

    public function create()
    {
        return view('pages.backend.topic.subtopic.create');
    }

    public function edit(Client $subTopicUid)
    {
        $subtopic = $this->subtopicRepo->findByUid($subTopicUid);

        return view('pages.backend.topic.subtopic.edit', compact('subtopic'));
    }

    public function getAll(): JsonResponse
    {
        $subtopics = $this->subtopicRepo->getAll();

        return response()->json([
            'data' => $subtopics
        ]);
    }

    public function store(StoreSubtopicRequest $request): JsonResponse
    {
        $subtopic = $this->subtopicRepo->store($request->validated());

        return response()->json([
            'message' => 'Subtopic created successfully',
            'data' => $subtopic
        ], Response::HTTP_CREATED);
    }

    public function show($subTopicUid): JsonResponse
    {
        $subtopic = $this->subtopicRepo->findByUid($subTopicUid);

        if (!$subtopic) {
            return response()->json([
                'message' => 'Subtopic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $subtopic
        ]);
    }

    public function update(StoreSubtopicRequest $request, SubTopic $subTopicUid): JsonResponse
    {
        $subtopic = $this->subtopicRepo->update($subTopicUid, $request->validated());

        if (!$subtopic) {
            return response()->json([
                'message' => 'Subtopic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Subtopic updated successfully',
            'data' => $subtopic
        ]);
    }

    public function destroy($subTopicUid): JsonResponse
    {
        $deleted = $this->subtopicRepo->delete($subTopicUid);

        if (!$deleted) {
            return response()->json([
                'message' => 'Subtopic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Subtopic deleted successfully'
        ]);
    }

    public function getByTopic($topicId)
    {
        $subtopics = $this->subtopicRepo->getByTopicId($topicId);

        return response()->json([
            'data' => $subtopics
        ]);
    }
}
