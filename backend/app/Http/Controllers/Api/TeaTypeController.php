<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeaType;
use Illuminate\Http\JsonResponse;

class TeaTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TeaType::orderBy('id')->get());
    }

    public function show(int $id): JsonResponse
    {
        $type = TeaType::with('teas')->find($id);
        if (!$type) {
            return response()->json(['message' => '茶类不存在'], 404);
        }
        return response()->json($type);
    }
}
