<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 対象のユーザーID（既存のユーザーIDを指定、またはループで複数人に適用してください）
        $userId = 1; 

        // 2026年6月1日から2026年7月31日までの期間を設定
        $startPeriod = Carbon::create(2026, 6, 1);
        $endPeriod = Carbon::create(2026, 7, 31);
        $period = CarbonPeriod::create($startPeriod, $endPeriod);

        $shifts = [];

        foreach ($period as $date) {
            // 平日（月曜日〜金曜日）のみ対象にする
            if ($date->isWeekday()) {
                $shifts[] = [
                    'user_id'    => $userId,
                    'work_date'  => $date->format('Y-m-d'),
                    'remote'     => false, // デフォルト値
                    'start_time' => '09:00:00',
                    'end_time'   => '17:30:00',
                    'break_time' => 60,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 大量インサート（一括挿入）
        if (!empty($shifts)) {
            DB::table('shifts')->insert($shifts);
        }
    }
}