<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon; 
use App\Models\WorkSchedule; 

class WorkScheduleController extends Controller

{
    public function index(Request $request)
    {
        // 1. タブの状態を取得（デフォルトは 'main'）
        $tab = $request->query('tab', 'main');

        // 2. 表示対象の月を取得（デフォルトは今月 'Y-m'）
        $monthParam = $request->query('month', Carbon::now()->format('Y-m'));
        
        try {
            $currentMonth = Carbon::parse($monthParam);
        } catch (\Exception $e) {
            $currentMonth = Carbon::now();
        }

        // 3. 先月・次月の文字列を生成
        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        // 4. 該当月の勤怠データを取得（※サンプルとしてログインユーザーのデータを想定）
        // $request->user()->attendances... のように絞り込むのが一般的です
        //$WorkSchedule = WorkSchedule::whereYear('date', $currentMonth->year)
            // ->whereMonth('date', $currentMonth->month)
            // ->orderBy('date', 'asc')
            // ->get();  えらーだよ

        // 5. サマリーエリアに渡すデータの計算（ダミー、または実データから集計）
        // ※実際はDBから合計値などを計算するか、モデルのメソッド等から取得してください
        $summary = [
            'total_work'      => '160:00', // 合計勤務時間
            'prescribed_work' => '160:00', // 規定労働時間
            'work_days'       => '20日',   // 勤務日数
            'total_break'     => '20:00',  // 休憩時間
            'late_early'      => '0:00',   // 遅刻早退時間
            'absent_days'     => '0日',    // 欠勤日数
        ];

        // 6. ビューにデータを渡して変数を展開
        return view('workSchedule.index', [
            'tab'          => $tab,
            'currentMonth' => $currentMonth->format('Y-m'),
            'prevMonth'    => $prevMonth,
            'nextMonth'    => $nextMonth,
           // 'workSchedule'  => $WorkSchedule,
            'summary'      => $summary,
        ]);
    }
}

