<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::query();
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        return response()->json($query->orderBy('id')->get());
    }

    public function show(int $id): JsonResponse
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => '文章不存在'], 404);
        }
        return response()->json($article);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->first();
        if (!$article) {
            return response()->json(['message' => '文章不存在'], 404);
        }
        return response()->json($article);
    }
}
