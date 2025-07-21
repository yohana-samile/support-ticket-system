<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SenderId;
use App\Repositories\Backend\SenderIdRepository;
use App\Http\Requests\Backend\SenderIdRequest as storeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SenderIdController extends Controller
{
    protected $senderIdRepo;

    public function __construct()
    {
        $this->senderIdRepo = app(SenderIdRepository::class);
    }

    public function index(): JsonResponse
    {
        $senderIds = $this->senderIdRepo->getAll();
        return response()->json(['data' => $senderIds]);
    }

    public function activeSenderIds($clientId): JsonResponse
    {
        $senderIds = $this->senderIdRepo->getActiveSenderIdsForClient($clientId);
        return response()->json(['data' => $senderIds]);
    }

    public function store(storeRequest $request): JsonResponse
    {
        $senderId = $this->senderIdRepo->store($request->validated());
        return response()->json([
            'message' => 'Sender ID created successfully',
            'data' => $senderId
        ], Response::HTTP_CREATED);
    }

    public function show(string $uid): JsonResponse
    {
        $senderId = $this->senderIdRepo->findByUid($uid);
        if (!$senderId) {
            return response()->json(['message' => 'Sender ID not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => $senderId]);
    }

    public function update(storeRequest $request, SenderId $senderId): JsonResponse
    {
        $senderId = $this->senderIdRepo->update($senderId, $request->validated());
        return response()->json([
            'message' => 'Sender ID updated successfully',
            'data' => $senderId
        ]);
    }

    public function destroy(SenderId $senderId): JsonResponse
    {
        $deleted = $this->senderIdRepo->delete($senderId);
        if (!$deleted) {
            return response()->json(['message' => 'Sender ID could not be deleted'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(['message' => 'Sender ID deleted successfully']);
    }
}
