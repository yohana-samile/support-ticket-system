<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SenderId;
use App\Repositories\Backend\SenderIdRepository;
use App\Http\Requests\Backend\SenderIdRequest as storeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SenderIdController extends Controller
{
    protected $senderIdRepo;

    public function __construct()
    {
        $this->senderIdRepo = app(SenderIdRepository::class);
    }

    public function index()
    {
        return view('pages.backend.sender.index');
    }

    public function create()
    {
        return view('pages.backend.sender.create');
    }

    public function store(storeRequest $request)
    {
        $sender = $this->senderIdRepo->store($request->validated());
        return redirect()->route('backend.sender_id.show', $sender->uid)->with('success', 'SenderIds added');
    }

    public function edit(SenderId $sender)
    {
        return view('pages.backend.sender.edit', compact('sender'));
    }

    public function show(SenderId $sender)
    {
        return view('pages.backend.sender.profile.show', compact('sender'));
    }

    public function update(storeRequest $request, SenderId $sender)
    {
        $this->senderIdRepo->update($sender, $request->validated());
        return redirect()->route('backend.sender_id.show', $sender->uid)->with('success', 'SenderIds updated');
    }

    public function destroy(SenderId $sender)
    {
        $this->senderIdRepo->delete($sender);
        return redirect()->route('backend.sender_id.index')->with('success', 'SenderIds deleted');
    }

    public function activeSenderIds($clientId): JsonResponse
    {
        $senderIds = $this->senderIdRepo->getActiveSenderIdsForClient($clientId);
        return response()->json(['data' => $senderIds]);
    }

    public function getAll(): JsonResponse
    {
        $senderIds = $this->senderIdRepo->getAll();
        return response()->json(['data' => $senderIds]);
    }

    public function getAllForDt(Request $request)
    {
        return DataTables::of($this->senderIdRepo->getAll($request->all()))
            ->addColumn('name', function($sender) {
                return $sender->sender_id;
            })
            ->addColumn('status_badge', function($sender) {
                return getStatusBadge($sender->is_active);
            })
            ->addColumn('created_at', function($sender) {
                return $sender->created_at->diffForHumans();
            })
            ->addColumn('actions', function($sender) {
                $actions = '<a href="'.route('backend.sender_id.show', $sender->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.sender_id.edit', $sender->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($sender->can_be_deleted) {
                    $formId = 'delete-sender_id-form-' . $sender->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$sender->uid.'\')">
                        <i class="fas fa-trash fa-sm"></i>
                     </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.sender_id.destroy', $sender->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['name', 'status_badge', 'created_at', 'actions'])->make(true);
    }
}
