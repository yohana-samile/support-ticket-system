<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Access\Client;
use App\Repositories\Backend\ClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Requests\Backend\StoreClientRequest as storeRequest;
use App\Http\Requests\Backend\UpdateClientRequest as updateRequest;

class ClientController extends Controller
{
    protected $clientRepo;

    public function __construct()
    {
        $this->clientRepo = app(ClientRepository::class);
    }

    public function index()
    {
        return view('pages.backend.client.index');
    }

    public function create()
    {
        return view('pages.backend.client.create');
    }

    public function edit(Client $clientUid)
    {
        $client = $this->clientRepo->findByUid($clientUid);
        return view('pages.backend.client.edit', compact('client'));
    }

    public function store(storeRequest $request): JsonResponse
    {
        $client = $this->clientRepo->store($request->validated());

        return response()->json([
            'message' => 'Client created successfully',
            'data' => $client
        ], Response::HTTP_CREATED);
    }

    public function show($uid)
    {
        $client = $this->clientRepo->findByUid($uid);

        if (!$client) {
            redirect()->back()->with('error', 'Client not found');
        }
        return view('pages.backend.client.show', compact('client'));
    }

    public function update(updateRequest $request, Client $client): JsonResponse
    {
        $client = $this->clientRepo->update($client, $request->validated());

        return response()->json([
            'message' => 'Client updated successfully',
            'data' => $client
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $deleted = $this->clientRepo->delete($client);

        if (!$deleted) {
            return response()->json([
                'message' => 'Client could not be deleted'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Client deleted successfully'
        ]);
    }

    public function getByService($serviceId): JsonResponse
    {
        $clients = $this->clientRepo->getByServiceId($serviceId);
        return response()->json([
            'data' => $clients
        ]);
    }

    public function getAll(): JsonResponse
    {
        $clients = $this->clientRepo->getAll();

        return response()->json([
            'data' => $clients
        ]);
    }
}
