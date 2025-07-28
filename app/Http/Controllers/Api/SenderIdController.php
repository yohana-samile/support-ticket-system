<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SenderId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SenderIdController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            // For single insertion
            'sender_id' => 'sometimes|required|string|max:11',
            'is_active' => 'nullable|boolean',

            // For bulk insertion
            'sender_ids' => 'sometimes|required|array',
            'sender_ids.*.sender_id' => 'required|string|max:11',
            'sender_ids.*.is_active' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();
        // Handle single record or bulk insert
        $data = isset($validated['sender_id']) ? [$validated] : $validated['sender_ids'];

        $createdIds = [];

        DB::transaction(function () use ($data, &$createdIds) {
            foreach ($data as $item) {
                $sender = SenderId::create([
                    'sender_id' => $item['sender_id'],
                    'is_active' => $item['is_active'] ?? true
                ]);
                $createdIds[] = $sender->id;
            }
        });

        return response()->json([
            'success' => true,
            'data' => [
                'count' => count($createdIds),
            ],
            'message' => count($createdIds) > 1
                ? 'Sender IDs successfully registered'
                : 'Sender ID successfully registered'
        ], 201);
    }
}
