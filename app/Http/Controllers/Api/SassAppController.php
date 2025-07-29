<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SaasApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SassAppController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            // For single insertion
            'name' => 'sometimes|required|string|unique:saas_apps,name|max:150',
            'abbreviation' => 'sometimes|required|string|unique:saas_apps,abbreviation|max:50',

            // For bulk insertion
            'apps' => 'sometimes|required|array',
            'apps.*.name' => 'required|string|unique:saas_apps,name|max:150',
            'apps.*.abbreviation' => 'required|string|unique:saas_apps,abbreviation|max:50',
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
        $data = isset($validated['name']) ? [$validated] : $validated['apps'];

        $createdIds = [];

        DB::transaction(function () use ($data, &$createdIds) {
            foreach ($data as $item) {
                $app = SaasApp::create([
                    'name' =>  $item['name'],
                    'abbreviation' => $item['abbreviation']
                ]);
                $createdIds[] = $app->id;
            }
        });

        return response()->json([
            'success' => true,
            'data' => [
                'count' => count($createdIds),
            ],
            'message' => count($createdIds) > 1
                ? 'SaaS apps successfully registered'
                : 'SaaS app successfully registered'
        ], 201);
    }
}
