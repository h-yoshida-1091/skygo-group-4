<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        // ランダムな日付を生成（例：過去30日以内）
        $date = fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d');
        
        // その日の朝8:00〜10:00の間にランダムで出勤
        $clockIn = Carbon::parse($date)->setTime(fake()->numberBetween(8, 10), fake()->numberBetween(0, 59));
        
        // 出勤時間から8〜10時間後に退勤（たまに想定外の残業があるシミュレート）
        $clockOut = (clone $clockIn)->addHours(fake()->numberBetween(8, 10))->addMinutes(fake()->numberBetween(0, 59));

        return [
            // 既存のユーザーからランダムに紐付け（なければ自動生成）
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'work_date' => $date,
            'clock_in' => $clockIn,
            // 90%の確率で退勤済みにし、10%は「まだ退勤ボタンを押していない（nullable）」状態を再現
            'clock_out' => fake()->boolean(90) ? $clockOut : null,
        ];
    }
}