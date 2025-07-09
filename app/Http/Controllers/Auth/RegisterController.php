<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Models\Access\Role;
use App\Providers\RouteServiceProvider;
use App\Repositories\Backend\UserRepository;

class RegisterController extends Controller
{
//    protected $redirectTo = RouteServiceProvider::HOME;
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = app(UserRepository::class);
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view("auth/register");
    }

    public function signup(UserRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $role = Role::getRoleByName('reporter');
            $validatedData['role_id'] = $role->id;
            $validatedData['is_reporter'] = true;
            $user = $this->userRepository->store($validatedData);

            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration successful',
                    'url_destination' => '/login',
                ], 201);
            }
        } catch (\Exception $e) {
            \Log::info("error: " . $e);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register user. Please try again.'
            ], 500);
        }
    }
}

