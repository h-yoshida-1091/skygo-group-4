<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * ダッシュボード画面の表示
     */
    public function index()
    {
        $userId = session('userId');

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
        $userId = session('userId'); // テスト用（後で Auth::id() に変更）
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
        $userId = session('userId');
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

    public function storeRequest(Request $request, $attendanceId)
    {
        // 1. バリデーション（修正理由を必須にするなど）
        $request->validate([
            'requested_clock_in'  => 'required|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'reason'              => 'required|string|max:500',
        ]);

        $userId = session('userId');
        
        // 元の勤怠データを取得
        $attendance = Attendance::findOrFail($attendanceId);

        // 画面から送られてきた時間（H:i）を、日付と結合して Y-m-d H:i:s の形にする
        $workDate = Carbon::parse($attendance->work_date)->format('Y-m-d');
        $requestedClockIn = Carbon::parse($workDate . ' ' . $request->input('requested_clock_in'));
        $requestedClockOut = $request->input('requested_clock_out') 
            ? Carbon::parse($workDate . ' ' . $request->input('requested_clock_out'))
            : null;

        // 2. attendance_requests テーブルに申請データを保存
        AttendanceRequest::create([
            'user_id'             => $userId,
            'attendance_id'       => $attendance->id,
            'work_date'           => $workDate,
            'requested_clock_in'  => $requestedClockIn,
            'requested_clock_out' => $requestedClockOut,
            'reason'              => $request->input('reason'),
            'status'              => 'pending', // デフォルトは承認待ち
        ]);

        return redirect()->back()->with('success', '打刻修正のリクエストを送信しました（承認待ち）。');
    }

    /**
     * 【修正】管理者が申請を「承認（approved）」したときの処理
     * （本来は管理者用画面のコントローラーに分けるのが理想ですが、一旦ここに記載します）
     */
    // public function approveRequest($requestId)
    // {
    //     // 申請データを取得
    //     $attendanceRequest = AttendanceRequest::findOrFail($requestId);

    //     // 1. 申請のステータスを「approved（承認）」に更新
    //     $attendanceRequest->update(['status' => 'approved']);

    //     // 2. 元の勤怠データ（attendancesテーブル）を申請された時間で上書きする！
    //     $attendance = Attendance::findOrFail($attendanceRequest->attendance_id);
    //     $attendance->update([
    //         'clock_in'  => $attendanceRequest->requested_clock_in,
    //         'clock_out' => $attendanceRequest->requested_clock_out,
    //     ]);

    //     return redirect()->back()->with('success', '申請を承認し、勤怠データを更新しました。');
    // }
}