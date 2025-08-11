<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpdateUserRequest as updateRequest;
use App\Http\Requests\Backend\User\StoreUserRequest as storeRequest;
use App\Models\Access\User;
use App\Repositories\Access\RoleRepository;
use App\Repositories\Backend\UserRepository;
use App\Traits\PhoneNumberValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class UserCrudController extends Controller
{
    use PhoneNumberValidation;
    protected $userRepo;
    protected $roleRepo;

    public function __construct()
    {
        $this->userRepo = app(UserRepository::class);
        $this->roleRepo = app(RoleRepository::class);
    }

    public function index()
    {
        return view('pages.backend.user.staff.index');
    }

    public function create()
    {
        $roles = $this->roleRepo->getActiveRoles();
        return view('pages.backend.user.staff.create', compact('roles'));
    }

    public function activeManagers(): JsonResponse
    {
        $users = $this->userRepo->getActiveManagers();
        return response()->json([
            'data' => $users
        ]);
    }

    public function store(storeRequest $request): JsonResponse
    {
        $input = $request->validated();
        try {
            $input['phone'] = $this->formatPhoneNumber($input['phone']);
            $user = $this->userRepo->store($input);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $uid): JsonResponse
    {
        $user = $this->userRepo->findByUid($uid);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $user
        ]);
    }

    public function update(updateRequest $request, User $user): JsonResponse
    {
        $user = $this->userRepo->update($user, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $deleted = $this->userRepo->delete($user);

        if (!$deleted) {
            return response()->json([
                'message' => 'User could not be deleted'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function getBySpecialization(string $specialization): JsonResponse
    {
        $managers = $this->userRepo->getManagersBySpecialization([$specialization]);
        return response()->json([
            'data' => $managers
        ]);
    }

    public function incrementFavorite(User $manager): JsonResponse
    {
        $user = $this->userRepo->incrementFavoriteCount($manager);

        return response()->json([
            'user' => 'Favorite count incremented',
            'data' => $user
        ]);
    }

    public function getAll(): JsonResponse
    {
        $users = $this->userRepo->getAll();

        return response()->json([
            'data' => $users
        ]);
    }

    public function getAllForDt()
    {
        return DataTables::of($this->userRepo->getAll())
            ->addColumn('name', function($user) {
                return '<a href="'.route('backend.user.show', $user->uid).'">'.Str::limit($user->name, 30).'</a>';
            })
            ->addColumn('created_at', function($user) {
                return $user->created_at->diffForHumans();
            })
            ->addColumn('manager_badge', function($user) {
                return getManagerBadge($user->is_super_admin);
            })
            ->addColumn('status_badge', function($user) {
                return getStatusBadge($user->is_active);
            })
            ->addColumn('actions', function($user) {
                $actions = '<a href="javascript:void(0)" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.user.edit', $user->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit" hidden>
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($user->can_be_deleted) {
                    $formId = 'delete-user-form-' . $user->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$user->uid.'\')">
                        <i class="fas fa-trash fa-sm"></i>
                     </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.user.destroy', $user->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['name', 'manager_badge', 'status_badge', 'actions'])->make(true);
    }
}
