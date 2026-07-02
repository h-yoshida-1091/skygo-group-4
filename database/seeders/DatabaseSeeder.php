<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 一般ユーザー
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'department' => '人事部',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // 管理者ユーザー
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'department' => '管理部',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->call([
            AttendanceSeeder::class,
        ]);
    }
}
