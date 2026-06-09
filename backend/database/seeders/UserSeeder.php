<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 使用明文密码，由 User 模型的 password hashed cast 负责哈希，避免双重哈希导致登录失败
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => '123456', 'role' => 'admin']
        );
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'User', 'password' => '123456', 'role' => 'user']
        );
    }
}
