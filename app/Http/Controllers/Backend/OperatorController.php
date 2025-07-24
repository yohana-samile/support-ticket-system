<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OperatorController extends Controller
{
    public function index()
    {
        return view('pages.backend.mno.index');
    }

    protected function getAllMnos()
    {
        return Operator::query()->where('is_active', true)->orderBy('created_at', 'Desc')->get();
    }

    public function getAll(): JsonResponse
    {
        $operators = $this->getAllMnos();
        return response()->json(['data' => $operators]);
    }

    public function getAllForDt()
    {
        $operator = $this->getAllMnos();
        return DataTables::of($operator)
            ->addColumn('name', function($operator) {
                return '<a href="javascript:void(0)">'.Str::limit($operator->name, 30).'</a>';
            })
            ->addColumn('status_badge', function($operator) {
                return getStatusBadge($operator->is_active);
            })
            ->addColumn('created_at', function($operator) {
                return $operator->created_at->diffForHumans();
            })->rawColumns(['name', 'status_badge', 'created_at'])->make(true);
    }
}
