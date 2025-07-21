<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTopicRequest as storeRequest;
use App\Models\Topic;
use App\Repositories\Backend\TopicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TopicController extends Controller
{

    protected $topicRepo;

    public function __construct()
    {
        $this->topicRepo = app(TopicRepository::class);
    }

    public function index()
    {
        $topics = $this->topicRepo->getAll();
        return view('pages.backend.topics.main.index', compact('topics'));
    }

    public function create()
    {
        return view('pages.backend.topic.create');
    }

    public function edit(Topic $topicUid)
    {
        $topic = $this->topicRepo->findByUid($topicUid);
        return view('pages.backend.topic.edit', compact('topic'));
    }

    public function store(storeRequest $request)
    {
        $topic = $this->topicRepo->store($request->validated());

        return response()->json([
            'message' => 'Topic created successfully',
            'data' => $topic
        ], 201);
    }

    public function show($topicUid)
    {
        $topic = $this->topicRepo->findByUid($topicUid);

        if (!$topic) {
            return response()->json([
                'message' => 'Topic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $topic
        ]);
    }

    public function update(storeRequest $request, $topicUid)
    {
        $topic = $this->topicRepo->update($topicUid, $request->validated());

        if (!$topic) {
            return response()->json([
                'message' => 'Topic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Topic updated successfully',
            'data' => $topic
        ]);
    }

    public function destroy($topicUid)
    {
        $deleted = $this->topicRepo->delete($topicUid);

        if (!$deleted) {
            return response()->json([
                'message' => 'Topic not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Topic deleted successfully'
        ]);
    }

    public function getAll()
    {
        $topics = $this->topicRepo->getAll();

        return response()->json([
            'data' => $topics
        ]);
    }

    public function getByService($serviceId): JsonResponse
    {
        $topics = $this->topicRepo->getByServiceId($serviceId);

        return response()->json([
            'data' => $topics
        ]);
    }

}
