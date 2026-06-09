<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Tea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function indexByTea(int $teaId): JsonResponse
    {
        $tea = Tea::find($teaId);
        if (!$tea) {
            return response()->json(['message' => '茶品不存在'], 404);
        }
        $comments = Comment::with('user:id,name,role')
            ->where('tea_id', $teaId)
            ->orderByDesc('id')
            ->get();
        return response()->json($comments);
    }

    public function store(Request $request, int $teaId): JsonResponse
    {
        $tea = Tea::find($teaId);
        if (!$tea) {
            return response()->json(['message' => '茶品不存在'], 404);
        }
        $v = Validator::make($request->all(), [
            'content' => 'required|string|min:1|max:500',
        ]);
        if ($v->fails()) {
            return response()->json(['message' => '校验失败', 'errors' => $v->errors()], 422);
        }

        $comment = Comment::create([
            'tea_id' => $teaId,
            'user_id' => $request->user()->id,
            'content' => trim($v->validated()['content']),
        ]);
        $comment->load('user:id,name,role');
        return response()->json($comment, 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $comment = Comment::with('user:id,role')->find($id);
        if (!$comment) {
            return response()->json(['message' => '评论不存在'], 404);
        }

        $user = $request->user();
        $isOwner = $comment->user_id === $user->id;
        $isAdmin = $user->role === 'admin';
        if (!$isOwner && !$isAdmin) {
            return response()->json(['message' => '无权限删除该评论'], 403);
        }

        $comment->delete();
        return response()->json(['message' => '已删除']);
    }
}

