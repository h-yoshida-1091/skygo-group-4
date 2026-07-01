<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 現在データベースにいる全ユーザーを取得する
        $users = User::all();

        // もしユーザーが1人も登録されていない場合は、警告を出して処理を抜ける
        if ($users->isEmpty()) {
            $this->command->warn('usersテーブルにユーザーが1人も登録されていないため、勤怠データは生成されませんでした。先にユーザーを登録するか、tinker等で作成してください。');
            return;
        }

        // 2. 既存のユーザーごとに20レコードずつ勤怠データを生成
        foreach ($users as $user) {
            Attendance::factory()
                ->count(20)
                ->create([
                    'user_id' => $user->id,
                ]);
        }
    }
}