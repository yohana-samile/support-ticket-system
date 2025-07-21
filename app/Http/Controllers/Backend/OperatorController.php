<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use Illuminate\Http\JsonResponse;

class OperatorController extends Controller
{
    public function index()
    {
        return view('pages.backend.mno.index');
    }

    public function getAll(): JsonResponse
    {
        $operators = Operator::query()->where('is_active', true)->get();
        return response()->json(['data' => $operators]);
    }
}
