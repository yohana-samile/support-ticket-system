<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpdateClientRequest as updateRequest;
use App\Http\Requests\Backend\User\StoreClientRequest as storeRequest;
use App\Models\Access\Client;
use App\Models\SenderId;
use App\Models\SubTopic;
use App\Repositories\Backend\ClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

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

    public function edit(Client $client)
    {
        return view('pages.backend.client.edit', compact('client'));
    }

    public function store(storeRequest $request)
    {
        $client = $this->clientRepo->store($request->validated());
        return redirect()->route('backend.client.show', $client->uid)->with('success', 'Client created successfully');
    }

    public function show(Client $client)
    {
        return view('pages.backend.client.profile.show', compact('client'));
    }

    public function update(updateRequest $request, Client $client)
    {
        $client = $this->clientRepo->update($client, $request->validated());
        return redirect()->route('backend.client.show', $client->uid)->with('success', 'Client updated successfully');
    }

    public function updatePassword(Request $request, Client $client)
    {
        $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed'],]);
        $client->update(['password' => $request->password]);
        return redirect()->back()->with('success', 'Client password updated successfully');
    }

    public function assignSenderId(Request $request, Client $client)
    {
        $request->validate([
            'sender_id' => ['required', 'array'],
            'sender_id.*' => ['exists:sender_ids,id'],
        ]);
        $client->senderIds()->syncWithoutDetaching($request->sender_id);
        return redirect()->back()->with('success', 'senderIds assigned to this client successfully');
    }

    public function detachSenderId(Request $request, $client, $senderId)
    {
        $client = Client::findOrFail($client);
        SenderId::findOrFail($senderId);

        $client->senderIds()->detach($senderId);
        return redirect()->back()->with('success', 'senderIds removed to this client');
    }
    public function destroy(Client $client)
    {
        $this->clientRepo->delete($client);
        return view('pages.backend.client.index')->with('success', 'Client deleted successfully');
    }

    public function getByService($serviceId): JsonResponse
    {
        $clients = $this->clientRepo->getByServiceId($serviceId);
        return response()->json([
            'data' => $clients
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $results = Client::query()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%");
                });
            })
            ->paginate(10);

        return response()->json([
            'data' => $results->items(),
            'next_page_url' => $results->nextPageUrl()
        ]);
    }

    public function getAll(): JsonResponse
    {
        $clients = $this->clientRepo->getAll();

        return response()->json([
            'data' => $clients
        ]);
    }

    public function getAllForDt()
    {
        return DataTables::of($this->clientRepo->getAllClientWithSenderIdCount())
            ->addColumn('name', function($client) {
                return '<a href="'.route('backend.client.show', $client->uid).'">'.Str::limit($client->name, 30).'</a>';
            })
            ->addColumn('email', function($client) {
                return $client->email;
            })
            ->addColumn('saas_app', function($client) {
                return $client->saas_app_name ?? 'N/A';
            })
            ->addColumn('created_at', function($client) {
                return $client->created_at->diffForHumans();
            })
            ->addColumn('status_badge', function($client) {
                return getStatusBadge($client->is_active);
            })
            ->addColumn('count', function($client) {
                return $client->senderIds->count() ?? 0;
            })
            ->addColumn('actions', function($client) {
                $actions = '<a href="'.route('backend.client.show', $client->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.client.edit', $client->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($client->can_be_deleted) {
                    $formId = 'delete-client-form-' . $client->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$client->uid.'\')">
                        <i class="fas fa-trash fa-sm"></i>
                     </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.client.destroy', $client->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['name', 'count', 'saas_app', 'email', 'status_badge', 'actions'])->make(true);
    }
}
