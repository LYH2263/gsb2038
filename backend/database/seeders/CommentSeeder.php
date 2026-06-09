<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Tea;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $tea = Tea::orderBy('id')->first();
        if (!$tea) return;

        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'user@example.com')->first();
        if (!$admin || !$user) return;

        $samples = [
            [$admin->id, '欢迎来到茶品百科！如有建议欢迎留言。'],
            [$user->id, '这款茶香气很舒服，回甘也不错，适合日常喝。'],
            [$user->id, '请问建议的冲泡水温是多少？有没有冷泡的做法？'],
        ];

        foreach ($samples as [$uid, $content]) {
            Comment::updateOrCreate(
                ['tea_id' => $tea->id, 'user_id' => $uid, 'content' => $content],
                []
            );
        }
    }
}

