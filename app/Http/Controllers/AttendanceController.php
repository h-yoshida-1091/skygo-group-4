<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * ダッシュボード画面の表示
     */
    public function index()
    {
        // 本来はログインユーザー（Auth::id()）で絞り込みますが、
        // 今回はテスト用に一旦usersテーブルの一番最初のユーザーのデータを取得します
        // ※ログイン機能を実装したら Auth::id() に書き換えてください
        $userId = 1; 

        // 勤怠履歴を日付が新しい順に取得
        $attendances = Attendance::where('user_id', $userId)
            ->orderBy('work_date', 'desc')
            ->get();

        return view('Attendance/dashboard', compact('attendances'));
    }

    /**
     * 出勤ボタンが押されたとき
     */
    public function clockIn(Request $request)
    {
        $userId = 1; // テスト用（後で Auth::id() に変更）
        $today = Carbon::today()->format('Y-m-d');

        // すでに今日出勤しているかチェック（二重打刻防止）
        $exists = Attendance::where('user_id', $userId)
            ->where('work_date', $today)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '本日はすでに出勤打刻済みです。');
        }

        // データを新規作成
        Attendance::create([
            'user_id' => $userId,
            'work_date' => $today,
            'clock_in' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', '出勤しました！');
    }

    /**
     * 退勤ボタンが押されたとき
     */
    public function clockOut(Request $request)
    {
        $userId = 1; // テスト用（後で Auth::id() に変更）
        $today = Carbon::today()->format('Y-m-d');

        // 今日の出勤レコードを探す
        $attendance = Attendance::where('user_id', $userId)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', '出勤データが見つかりません。');
        }

        if ($attendance->clock_out) {
            return redirect()->back()->with('error', '本日はすでに退勤打刻済みです。');
        }

        // 退勤時間を更新
        $attendance->update([
            'clock_out' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', '退勤しました！お疲れ様でした。');
    }

    /**
     * ポップアップ（モーダル）からの修正
     */
    public function update(Request $request, $id)
    {
        // バリデーション（備考の入力を必須にする場合など）
        $request->validate([
            'note' => 'required|string|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);
        
        // 備考（メモ）などを更新
        // ※もし時間を修正させる場合は、リクエストから時間を受け取って更新します
        $attendance->update([
            'note' => $request->input('note'),
        ]);

        return redirect()->back()->with('success', '勤怠データを修正しました。');
    }
}