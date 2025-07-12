<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StorePaymentChannelRequest;
use App\Http\Requests\Backend\UpdatePaymentChannelRequest;
use App\Models\PaymentChannel;
use App\Repositories\Backend\PaymentChannelRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PaymentChannelController extends Controller
{
    protected PaymentChannelRepository $channelRepo;

    public function __construct()
    {
        $this->channelRepo = app(PaymentChannelRepository::class);
    }

    public function index()
    {
        return view('pages.backend.payment_channel.index');
    }

    public function create()
    {
        return view('pages.backend.payment_channel.create');
    }

    public function edit($channelUid)
    {
        $channel = $this->channelRepo->findByUid($channelUid);
        return view('pages.backend.payment_channel.create', compact('channel'));
    }

    public function activeChannels(): JsonResponse
    {
        $channels = $this->channelRepo->getActiveChannels();

        return response()->json([
            'data' => $channels
        ]);
    }

    public function store(StorePaymentChannelRequest $request): JsonResponse
    {
        $channel = $this->channelRepo->store($request->validated());

        return response()->json([
            'message' => 'Payment channel created successfully',
            'data' => $channel
        ], Response::HTTP_CREATED);
    }

    public function show(string $uid): JsonResponse
    {
        $channel = $this->channelRepo->findByUid($uid);

        if (!$channel) {
            return response()->json([
                'message' => 'Payment channel not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $channel
        ]);
    }

    public function update(UpdatePaymentChannelRequest $request, PaymentChannel $channel): JsonResponse
    {
        $channel = $this->channelRepo->update($channel, $request->validated());

        return response()->json([
            'message' => 'Payment channel updated successfully',
            'data' => $channel
        ]);
    }

    public function destroy(PaymentChannel $channel): JsonResponse
    {
        $deleted = $this->channelRepo->delete($channel);

        if (!$deleted) {
            return response()->json([
                'message' => 'Payment channel could not be deleted'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Payment channel deleted successfully'
        ]);
    }

    public function findByCode(string $code): JsonResponse
    {
        $channel = $this->channelRepo->findByCode($code);

        if (!$channel) {
            return response()->json([
                'message' => 'Payment channel not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $channel
        ]);
    }

    public function getAll(): JsonResponse
    {
        $channels = $this->channelRepo->getAll();

        return response()->json([
            'data' => $channels
        ]);
    }
}
