<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use Illuminate\Http\JsonResponse;

class OperatorController extends Controller
{
    public function getAll(): JsonResponse
    {
        $codeId = Code::query()->where('name', 'Mobile Operator')->value('id');
        $operators = CodeValue::query()->where('code_id', $codeId)->get();
        return response()->json(['data' => $operators]);
    }
}
