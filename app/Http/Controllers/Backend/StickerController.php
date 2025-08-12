<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Sticker;
use App\Http\Requests\Backend\StoreStickerRequest as storeRequest;
use App\Models\System\CodeValue;
use App\Repositories\Backend\StickerRepository;
use App\Repositories\Backend\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class StickerController extends Controller
{
    protected $stickerRepo;
    protected $userRepo;
    public function __construct()
    {
        $this->stickerRepo = app(StickerRepository::class);
        $this->userRepo = app(UserRepository::class);
    }

    public function index()
    {
        return view('pages.backend.stickers.index');
    }

    public function create()
    {
        $users = $this->userRepo->getActiveForSticker();
        return view('pages.backend.stickers.create', compact('users'));
    }

    public function edit(Sticker $sticker)
    {
        $users = $this->userRepo->getActiveForSticker();
        return view('pages.backend.stickers.edit', compact('sticker', 'users'));
    }

    public function store(storeRequest $request)
    {
        $status = CodeValue::query()->where('reference', 'STICKER_STATUS01')->value('name');
        $input = $request->validated();
        $input['user_id'] = user_id();
        $input['status'] = $status;
        $input['is_private'] = $input['is_private'] ?? false;
        if (isset($input['target_user_id']) && $input['target_user_id'] === 'all') {
            $input['is_for_all'] = true;
        }
        $this->stickerRepo->store($input);
        return redirect()->back()->with('success', __('messages.sticker_created_successfully'));
    }

    public function update(storeRequest $request, Sticker $sticker)
    {
        $status = CodeValue::query()->where('reference', 'STICKER_STATUS01')->value('name');
        $input = $request->validated();
        $input['user_id'] = user_id();
        $input['status'] = $status;
        $input['is_private'] = $input['is_private'] ?? false;
        if (isset($input['target_user_id']) && $input['target_user_id'] === 'all') {
            $input['is_for_all'] = true;
        }
        $this->stickerRepo->update($sticker, $input);
        return redirect()->back()->with('success', __('messages.sticker_updated_successfully'));
    }

    public function delete(Sticker $sticker)
    {
        $this->stickerRepo->delete($sticker);
        return redirect()->route('backend.stickers.index')->with('success', __('messages.sticker_deleted_successfully'));
    }

    public function show(Sticker $sticker)
    {
        return view('pages.backend.stickers.profile.profile', compact('sticker'));
    }

    public function getAllForDt(Request $request)
    {
        return DataTables::of($this->stickerRepo->getAll($request->all()))
            ->addColumn('note', function($sticker) {
                return '<a href="'.route('backend.stickers.show', $sticker->uid).'">'
                    . Str::limit(strip_tags($sticker->note), 30)
                    . '</a>';
            })
            ->addColumn('status_badge', function($sticker) {
                $status = $sticker->status;
                $badgeClass = [
                    'active' => 'success',
                    'archived' => 'warning',
                    'done' => 'primary'
                ][$status] ?? 'secondary';
                return '<span class="badge badge-'.$badgeClass.'">'.ucfirst($status).'</span>';
            })
            ->addColumn('private_badge', function($sticker) {
                return getManagerBadge($sticker->is_private);
            })
            ->addColumn('created_at', function($sticker) {
                return $sticker->created_at->diffForHumans();
            })
            ->addColumn('actions', function($sticker) {
                $actions = '<a href="'.route('backend.stickers.show', $sticker->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.stickers.edit', $sticker->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                $formId = 'delete-stickers-form-' . $sticker->uid;
                $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$sticker->uid.'\')">
                    <i class="fas fa-trash fa-sm"></i>
                 </a>';

                $actions .= csrf_field()
                    . method_field('DELETE')
                    . '<form id="'.$formId.'" action="'.route('backend.stickers.destroy', $sticker->uid).'" method="POST" style="display: none;">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '</form>';
                return $actions;
            })->rawColumns(['note', 'status_badge', 'private_badge', 'actions'])->make(true);
    }
}
