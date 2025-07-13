<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;

class NotificationChannelController extends Controller
{
    public function getAll(): JsonResponse
    {
        $channels = Channel::query()->where('is_active', true)->get();
        return response()->json(['data' => $channels]);
    }
}
