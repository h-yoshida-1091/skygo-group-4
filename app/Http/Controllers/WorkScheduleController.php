<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon; 
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth; // 💡 追加：Authファサードをインポート

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

        // 4. 該当月の勤怠データを取得
        $workSchedule = collect(); // デフォルトは空のコレクション
        
        // 💡 Auth::check() と Auth::id() に修正
        if (Auth::check()) {
            $workSchedule = Attendance::where('user_id', Auth::id())
                ->whereYear('work_date', $currentMonth->year)
                ->whereMonth('work_date', $currentMonth->month)
                ->orderBy('work_date', 'asc')
                ->get();
        }

        // 5. サマリーエリアに渡すデータの計算（ダミー）
        $summary = [
            'total_work'      => '160:00',
            'prescribed_work' => '160:00',
            'work_days'       => '20日',
            'total_break'     => '20:00',
            'late_early'      => '0:00',
            'absent_days'     => '0日',
        ];

        // 6. ビューにデータを渡して変数を展開
        return view('workSchedule.index', [
            'tab'          => $tab,
            'currentMonth' => $currentMonth->format('Y-m'),
            'prevMonth'    => $prevMonth,
            'nextMonth'    => $nextMonth,
            'workSchedule' => $workSchedule,
            'summary'      => $summary,
        ]);
    }
}