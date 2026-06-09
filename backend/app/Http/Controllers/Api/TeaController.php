<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tea::with('teaType');
        if ($request->has('tea_type_id')) {
            $query->where('tea_type_id', $request->tea_type_id);
        }
        return response()->json($query->orderBy('id')->get());
    }

    public function show(int $id): JsonResponse
    {
        $tea = Tea::with('teaType')->find($id);
        if (!$tea) {
            return response()->json(['message' => '茶品不存在'], 404);
        }
        return response()->json($tea);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => '仅管理员可新增茶品'], 403);
        }
        $data = $this->gatherTeaData($request);
        if (
            array_key_exists('image_url', $data)
            && !empty($data['image_url'])
        ) {
            return response()->json(['message' => '仅支持上传图片，不支持填写图片地址'], 422);
        }
        $v = Validator::make($data, [
            'name' => 'required|string|max:255',
            'tea_type_id' => 'nullable|exists:tea_types,id',
            'description' => 'nullable|string',
            'brewing_tip' => 'nullable|string',
            'tasting_notes' => 'nullable|string',
            'origin' => 'nullable|string|max:255',
        ]);
        if ($v->fails()) {
            return response()->json(['message' => '校验失败', 'errors' => $v->errors()], 422);
        }
        $uploadedImageUrl = null;
        if ($request->hasFile('image')) {
            $imgError = $this->validateImage($request->file('image'));
            if ($imgError) {
                return response()->json(['message' => $imgError], 422);
            }
            $uploadedImageUrl = $this->storeUploadedImage($request->file('image'));
        }
        $validated = $v->validated();
        if ($uploadedImageUrl) {
            $validated['image_url'] = $uploadedImageUrl;
        }
        return response()->json(Tea::create($validated), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => '仅管理员可编辑茶品'], 403);
        }
        $tea = Tea::find($id);
        if (!$tea) {
            return response()->json(['message' => '茶品不存在'], 404);
        }
        $data = $this->gatherTeaData($request);
        if (array_key_exists('image_url', $data) && !empty($data['image_url'])) {
            return response()->json(['message' => '仅支持上传图片，不支持填写图片地址'], 422);
        }
        $v = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'tea_type_id' => 'nullable|exists:tea_types,id',
            'description' => 'nullable|string',
            'brewing_tip' => 'nullable|string',
            'tasting_notes' => 'nullable|string',
            'origin' => 'nullable|string|max:255',
        ]);
        if ($v->fails()) {
            return response()->json(['message' => '校验失败', 'errors' => $v->errors()], 422);
        }
        $uploadedImageUrl = null;
        if ($request->hasFile('image')) {
            $imgError = $this->validateImage($request->file('image'));
            if ($imgError) {
                return response()->json(['message' => $imgError], 422);
            }
            $uploadedImageUrl = $this->storeUploadedImage($request->file('image'));
            $this->deleteTeaImageIfLocal($tea->image_url);
        }
        $validated = $v->validated();
        if ($uploadedImageUrl) {
            $validated['image_url'] = $uploadedImageUrl;
        }
        $tea->update($validated);
        return response()->json($tea);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => '仅管理员可删除茶品'], 403);
        }
        $tea = Tea::find($id);
        if (!$tea) {
            return response()->json(['message' => '茶品不存在'], 404);
        }
        $tea->delete();
        return response()->json(['message' => '已删除']);
    }

    /** 从请求中收集茶品数据（支持 JSON 与 multipart） */
    private function gatherTeaData(Request $request): array
    {
        $data = $request->all();
        if (isset($data['tea_type_id']) && $data['tea_type_id'] !== '') {
            $data['tea_type_id'] = (int) $data['tea_type_id'];
        }
        return $this->normalizeTeaRequest($data);
    }

    /** 校验上传图片：类型与大小，返回错误信息或 null */
    private function validateImage(\Illuminate\Http\UploadedFile $file): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowed, true)) {
            return '请上传 JPG、PNG、GIF 或 WebP 格式的图片';
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            return '图片大小不能超过 2MB';
        }
        return null;
    }

    /** 保存上传图片到 public/images/teas，返回可访问路径 */
    private function storeUploadedImage(\Illuminate\Http\UploadedFile $file): string
    {
        $dir = public_path('images/teas');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $ext = 'jpg';
        }
        $name = time() . '_' . Str::random(8) . '.' . $ext;
        $file->move($dir, $name);
        return '/images/teas/' . $name;
    }

    /** 若 image_url 为本地上传路径则删除文件（避免堆积） */
    private function deleteTeaImageIfLocal(?string $imageUrl): void
    {
        if (!$imageUrl || !str_starts_with($imageUrl, '/images/teas/')) {
            return;
        }
        $path = public_path($imageUrl);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /** 将空字符串、字符串 "null" 转为 null */
    private function normalizeTeaRequest(array $data): array
    {
        foreach (['tea_type_id', 'origin', 'image_url', 'description', 'brewing_tip', 'tasting_notes'] as $key) {
            if (array_key_exists($key, $data) && ($data[$key] === '' || $data[$key] === 'null')) {
                $data[$key] = null;
            }
        }
        return $data;
    }
}
