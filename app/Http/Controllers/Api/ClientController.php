<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Access\Client;
use App\Models\SaasApp;
use App\Models\SenderId;
use App\Repositories\Backend\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    protected $clientRepo;

    public function __construct(ClientRepository $clientRepo)
    {
        $this->clientRepo = $clientRepo;
    }

    public function store(Request $request)
    {
        // Determine if this is a bulk insert
        $isBulk = $request->has('clients');

        $rules = $isBulk ? [
            // Bulk insertion rules
            'clients' => 'required|array',
            'clients.*.name' => 'required|string|max:255',
            'clients.*.email' => 'required|email|unique:clients,email',
            'clients.*.phone' => 'nullable|string|max:20',
            'clients.*.saas_app_name' => 'required|exists:saas_apps,name',
            'clients.*.is_active' => 'nullable|boolean',
            'clients.*.sender_ids' => 'sometimes|array',
            'clients.*.sender_ids.*' => 'string|max:11',
        ] : [
            // Single insertion rules
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'saas_app_name' => 'required|exists:saas_apps,name',
            'is_active' => 'nullable|boolean',
            'sender_ids' => 'sometimes|array',
            'sender_ids.*' => 'string|max:11',
        ];

        $messages = [
            'name.required' => __('validation.name_required'),
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already in use.',
            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than 20 characters.',
            'saas_app_name.required' => 'The SaaS app name is required.',
            'saas_app_name.exists' => 'The specified SaaS app does not exist.',
            'is_active.boolean' => 'The active status must be a boolean.',
            'sender_ids.*.string' => 'Each sender ID must be a string.',
            'sender_ids.*.max' => 'Each sender ID may not be greater than 11 characters.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();

        // Handle single record or bulk insert
        $data = $isBulk ? $validated['clients'] : [$validated];

        $createdIds = [];
        $createdClients = [];

        DB::transaction(function () use ($data, &$createdIds, &$createdClients) {
            foreach ($data as $item) {
                $saasApp = SaasApp::where('name', $item['saas_app_name'])->firstOrFail();

                $password = $this->clientRepo->generatePassword();

                $client = Client::create([
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'phone' => $item['phone'] ?? null,
                    'saas_app_id' => $saasApp->id,
                    'is_active' => $item['is_active'] ?? true,
                    'password' => $password,
                ]);

                // Process sender IDs if provided
                if (!empty($item['sender_ids'])) {
                    foreach ($item['sender_ids'] as $senderIdValue) {

                        // Find or create sender ID
                        $senderId = SenderId::firstOrCreate(
                            ['sender_id' => $senderIdValue],
                            ['is_active' => true]
                        );

                        // Attach to client without detaching existing ones
                        $client->senderIds()->syncWithoutDetaching([$senderId->id]);
                    }
                }

                $createdIds[] = $client->id;
                $createdClients[] = [
                    'client' => $client,
                    'password' => $password
                ];
            }
        });

        // Send emails after transaction completes successfully
        foreach ($createdClients as $item) {
            $this->clientRepo->sendEmailWithPassword($item['client'], $item['password']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'count' => count($createdIds),
//                'ids' => $createdIds
            ],
            'message' => count($createdIds) > 1
                ? 'Clients successfully registered'
                : 'Client successfully registered'
        ], 201);
    }
}
