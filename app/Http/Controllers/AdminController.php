<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShiftRequest;
use App\Models\AttendanceRequest;
use App\Models\Shift;

class AdminController extends Controller
{
    public function dashboard()
    {
        $shiftRequests = ShiftRequest::with('user')
            ->latest()
            ->get();

        $attendanceRequests = AttendanceRequest::with('user')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('shiftRequests', 'attendanceRequests'));
    }

    public function approveShift($id)
    {
        $shift = ShiftRequest::findOrFail($id);

        // shiftsへ保存（必要なら）
        Shift::create([
            'user_id' => $shift->user_id,
            'work_date' => $shift->work_date,
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
        ]);

        // request削除
        $shift->delete();

        return back();
    }

    public function rejectShift(Request $request, $id)
    {
        $shift = ShiftRequest::findOrFail($id);

        $shift->status = 'rejected';
        $shift->comment = $request->comment;
        $shift->save();

        return back();
    }

    /*打刻修正 承認*/
    public function approveAttendance($id)
    {
        $attendance = AttendanceRequest::findOrFail($id);

        $attendance->status = 'approved';
        $attendance->comment = null;
        $attendance->save();

        return back();
    }

    /*打刻修正 差し戻し*/
    public function rejectAttendance(Request $request, $id)
    {
        $attendance = AttendanceRequest::findOrFail($id);

        $attendance->status = 'rejected';
        $attendance->comment = $request->comment;
        $attendance->save();

        return back();
    }
}
