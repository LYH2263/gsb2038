<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($v->fails()) {
            return response()->json(['message' => '校验失败', 'errors' => $v->errors()], 422);
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'user',
        ]);
        return response()->json(['message' => '注册成功，请登录'], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json(['message' => '校验失败', 'errors' => $v->errors()], 422);
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => '邮箱或密码错误'], 401);
        }
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => '已退出登录']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
