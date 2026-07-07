<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShiftRequest;
use App\Models\AttendanceRequest;
use App\Models\Shift;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $shiftRequests = ShiftRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('user_id')
            ->orderBy('work_date')
            ->get();

        $monthlyShiftRequests = $shiftRequests->groupBy(function ($req) {
            return $req->user_id . '-' . Carbon::parse($req->work_date)->format('Y-m');
        });

        $attendanceRequests = AttendanceRequest::with('user')
            ->latest()
            ->get();

        return view('admin.dashboard', compact(
            'monthlyShiftRequests',
            'attendanceRequests'
        ));
    }

    public function approveShiftMonth(Request $request)
    {
        $shiftRequests = ShiftRequest::where('user_id', $request->user_id)
            ->whereYear('work_date', $request->year)
            ->whereMonth('work_date', $request->month)
            ->where('status', 'pending')
            ->get();

        foreach ($shiftRequests as $shiftRequest) {
            Shift::updateOrCreate(
                [
                    'user_id' => $shiftRequest->user_id,
                    'work_date' => $shiftRequest->work_date,
                ],
                [
                    'remote' => $shiftRequest->remote,
                    'start_time' => $shiftRequest->start_time,
                    'end_time' => $shiftRequest->end_time,
                ]
            );

            $shiftRequest->status = 'approved';
            $shiftRequest->save();
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'シフト申請を承認しました。');
    }

    public function rejectShiftMonth(Request $request)
    {
        $shiftRequests = ShiftRequest::where('user_id', $request->user_id)
            ->whereYear('work_date', $request->year)
            ->whereMonth('work_date', $request->month)
            ->where('status', 'pending')
            ->get();

        foreach ($shiftRequests as $shiftRequest) {
            $shiftRequest->status = 'rejected';
            $shiftRequest->comment = $request->comment;
            $shiftRequest->save();
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'シフト申請を差し戻しました。');
    }

    public function approveAttendance($id)
    {
        $attendance = AttendanceRequest::findOrFail($id);

        $attendance->status = 'approved';
        $attendance->comment = null;
        $attendance->save();

        return back()->with('success', '打刻修正申請を承認しました。');
    }

    public function rejectAttendance(Request $request, $id)
    {
        $attendance = AttendanceRequest::findOrFail($id);

        $attendance->status = 'rejected';
        $attendance->comment = $request->comment;
        $attendance->save();

        return back()->with('success', '打刻修正申請を差し戻しました。');
    }
}