<?php

namespace App\Http\Controllers\Backend\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Access\RoleRequest;
use App\Models\Access\Role;
use App\Repositories\Access\PermissionRepository;
use App\Repositories\Access\RoleRepository;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;


class RoleController extends  Controller
{
    protected $role_repo;

    public function __construct() {
        $this->role_repo = new RoleRepository();
        //$this->middlewareRole();
    }

    public function index()
    {
        return view('pages.backend.access.role.index');
    }

    public function create()
    {
        $permissions = app(PermissionRepository::class)->getAll();
        $role = null;
        return view('pages.backend.access.role.create')->with('permissions', $permissions)->with('role', $role);
    }

    public function store(RoleRequest $request)
    {
        $input = $request->all();
        $input['name'] = Str::slug($input['display_name'], '_');
        $role = $this->role_repo->store($input);
        if (isset($input['permissions'])) {
            $role->permissions()->sync($input['permissions']);
        }
        return redirect()->route('backend.role.profile', ['role' => $role->uid])->with('success', __('label.role_created'));
    }

    public function edit(Role $role)
    {
        $permissions = app(PermissionRepository::class)->getAll();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('pages.backend.access.role.edit')
            ->with('permissions', $permissions)
            ->with('rolePermissions', $rolePermissions)
            ->with('role', $role);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $input = $request->all();
        $role = $this->role_repo->update($input, $role);

        if (isset($input['permissions'])){
            $role->permissions()->sync($input['permissions']);
        }
        else{
            $role->permissions()->sync([]);
        }

        return redirect()->route('backend.role.profile', ['role' => $role->uid])->with('success', __('label.role_updated'));
    }

    public function profile(Role $role) {
        $permissions = app(PermissionRepository::class)->getPermissionsByRole($role);
        return view('pages.backend.access.role.profile.profile')->with('permissions', $permissions)->with('role', $role);
    }

    public function show(Role $role)
    {
        return $this->profile($role);
    }

    public function delete(Role $role)
    {
        $this->role_repo->delete($role);
        return redirect()->route('backend.role.index')->with('success', __('messages.role_deleted'));
    }

    public function roleUser(Role $role)
    {
        $users = $role->users()->with('roles', 'permissions')->get();
        return view('pages.backend.access.role.users', [
            'role' => $role,
            'users' => $users,
        ]);
    }

    public function roleUsersPreview(Role $role)
    {
        $users = $role->users()->limit(5)->get();
        return response()->json([
            'html' => view('pages.backend.access.role.users_preview', compact('users'))->render()
        ]);
    }

    public function getAllForDt()
    {
        $result_list = $this->role_repo->getAllForDt();
        return DataTables::of($result_list)
            ->addIndexColumn()
            ->editColumn('name', function ($result_list) {
                return $result_list->name;
            })
            ->editColumn('description', function ($result_list) {
                return $result_list->description;
            })
            ->editColumn('isadmin', function ($result_list) {
                return getManagerBadge($result_list->isadmin);
            })

            ->editColumn('isactive', function ($result_list) {
                return getStatusBadge($result_list->isactive);
            })
            ->rawColumns(['isactive', 'isadmin'])
            ->make(true);
    }

//    protected function middlewareRole()
//    {
//        $this->middleware('access.routeNeedsPermission:manage_roles_permissions,all_functions', [
//            'only' => ['index', 'create', 'store', 'edit', 'update', 'delete', 'roleUser', 'roleUsersPreview']
//        ]);
//    }
}
