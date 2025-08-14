<?php

namespace App\Http\Controllers\Backend\Access;
use App\Http\Controllers\Controller;
use App\Models\Access\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends  Controller
{
    public function __construct() {
        $this->middleware('access.routeNeedsPermission:manage_roles_permissions', [
            'only' => ['index', 'getAllForDt']
        ]);
    }

    public function index()
    {
        return view('backend.access.permission.index');
    }

    public function getAllForDt()
    {
        $result_list = Permission::getAllPermissions();

        return DataTables::of($result_list)
            ->addIndexColumn()
            ->editColumn('display_name', function ($result_list) {
                return $result_list->display_name;
            })
            ->editColumn('description', function ($result_list) {
                return $result_list->description;
            })
            ->editColumn('isadmin', function ($result_list) {
                return boolean_badge($result_list->isadmin);
            })
            ->editColumn('isactive', function ($result_list) {
                return boolean_badge($result_list->isactive);
            })
            ->rawColumns(['isactive', 'isadmin'])
            ->make(true);
    }
}
