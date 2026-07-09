<?php

namespace App\Http\Controllers;

use App\Models\UserCharacter;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        if (!session()->has('userId')) {
            return redirect('/login');
        }

        $userId = session('userId');
        $today = Carbon::today()->format('Y-m-d');

        $attendances = Attendance::where('user_id', $userId)
            ->orderBy('work_date', 'desc')
            ->get();

        $character = UserCharacter::where('user_id', $userId)->first();

        $todayShift = Shift::where('user_id', $userId)
            ->whereDate('work_date', $today)
            ->first();

        return view('Attendance.dashboard', compact(
            'attendances',
            'character',
            'todayShift'
        ));
    }

    public function clockIn(Request $request)
    {
        $userId = session('userId');
        $today = Carbon::today()->format('Y-m-d');

        $exists = Attendance::where('user_id', $userId)
            ->where('work_date', $today)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '本日はすでに出勤打刻済みです。');
        }

        Attendance::create([
            'user_id' => $userId,
            'work_date' => $today,
            'clock_in' => Carbon::now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', '出勤しました。')
            ->with('sound', 'clock-in');
    }

    public function clockOut(Request $request)
    {
        $userId = session('userId');
        $today = Carbon::today()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $userId)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', '出勤データが見つかりません。');
        }

        if ($attendance->clock_out) {
            return redirect()->back()->with('error', '本日はすでに退勤打刻済みです。');
        }

        $clockOutTime = Carbon::now();

        $attendance->update([
            'clock_out' => $clockOutTime,
        ]);

        $clockInTime = Carbon::parse($attendance->clock_in);
        $workMinutes = $clockInTime->diffInMinutes($clockOutTime);
        $addExp = floor($workMinutes / 10);

        $character = UserCharacter::where('user_id', $userId)->first();

        $message = '退勤しました！お疲れ様でした。';

        if ($character) {
            $character->total_work_time += $workMinutes;
            $character->login_count += 1;
            $character->exp += $addExp;

            $levelUpCount = 0;

            while ($character->exp >= $character->level * 100) {
                $character->exp -= $character->level * 100;
                $character->level += 1;
                $levelUpCount++;
            }

            if ($character->level >= 50) {
                $character->image = 'ライオン.png';
            }

            if ($character->level >= 150) {
                $character->title = '伝説の社畜';
            } elseif ($character->level >= 140) {
                $character->title = '生けるタイムカード';
            } elseif ($character->level >= 130) {
                $character->title = '会社の守護神';
            } elseif ($character->level >= 120) {
                $character->title = '部長より会社にいる人';
            } elseif ($character->level >= 110) {
                $character->title = '勤怠管理神';
            } elseif ($character->level >= 100) {
                $character->title = '社畜エリート';
            } elseif ($character->level >= 90) {
                $character->title = '定時退社の伝説';
            } elseif ($character->level >= 80) {
                $character->title = 'コーヒー中毒';
            } elseif ($character->level >= 70) {
                $character->title = 'エクセルの使徒';
            } elseif ($character->level >= 60) {
                $character->title = '会議の生き証人';
            } elseif ($character->level >= 50) {
                $character->title = '有給ハンター';
            } elseif ($character->level >= 40) {
                $character->title = 'タイムカードマスター';
            } elseif ($character->level >= 30) {
                $character->title = '残業ビギナー';
            } elseif ($character->level >= 20) {
                $character->title = '一人前社員';
            } elseif ($character->level >= 10) {
                $character->title = 'コツコツワーカー';
            } else {
                $character->title = '新人ワーカー';
            }

            $character->save();

            $workHours = floor($workMinutes / 60);
            $message .= " {$workHours}時間勤務で {$addExp}EXP 獲得しました。";

            if ($levelUpCount > 0) {
                $message .= " Lv.{$character->level} にレベルアップしました！";
            }
        }

        return redirect()
            ->route('dashboard')
            ->with('success', $message)
            ->with('sound', 'clock-out');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'note' => $request->input('note'),
        ]);

        return redirect()->back()->with('success', '勤怠データを修正しました。');
    }

    public function storeRequest(Request $request, $attendanceId)
    {
        $request->validate([
            'requested_clock_in'  => 'required|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'reason'              => 'required|string|max:500',
        ]);

        $userId = session('userId');

        $attendance = Attendance::findOrFail($attendanceId);

        $workDate = Carbon::parse($attendance->work_date)->format('Y-m-d');
        $requestedClockIn = Carbon::parse($workDate . ' ' . $request->input('requested_clock_in'));
        $requestedClockOut = $request->input('requested_clock_out')
            ? Carbon::parse($workDate . ' ' . $request->input('requested_clock_out'))
            : null;

        // 差し戻し済みの古い申請を削除する
        AttendanceRequest::where('user_id', $userId)
            ->where('attendance_id', $attendance->id)
            ->where('status', 'rejected')
            ->delete();

        // 新しく承認待ちとして作成する
        AttendanceRequest::create([
            'user_id'             => $userId,
            'attendance_id'       => $attendance->id,
            'work_date'           => $workDate,
            'requested_clock_in'  => $requestedClockIn,
            'requested_clock_out' => $requestedClockOut,
            'reason'              => $request->input('reason'),
            'status'              => 'pending',
        ]);

        return redirect()->back()->with('success', '打刻修正のリクエストを送信しました（承認待ち）。');
    }
}
