<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpdateUserRequest as updateRequest;
use App\Http\Requests\Backend\User\StoreUserRequest as storeRequest;
use App\Models\Access\User;
use App\Repositories\Backend\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserCrudController extends Controller
{
    protected $userRepo;

    public function __construct()
    {
        $this->userRepo = app(UserRepository::class);
    }

    public function index()
    {
        return view('pages.backend.user.index');
    }

    public function create()
    {
        return view('pages.backend.user.create');
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
        $user = $this->userRepo->store($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_CREATED);
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
}
