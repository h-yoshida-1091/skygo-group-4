<?php

namespace App\Http\Controllers;

use App\Models\ShiftRequest;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $userId = session('userId');

        $shiftRequests = ShiftRequest::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->get();

        if ($shiftRequests->isEmpty()) {
            $shiftStatusText = '未提出';
        } elseif ($shiftRequests->contains('status', 'approved')) {
            $shiftStatusText = '承認済み';
        } elseif ($shiftRequests->contains('status', 'pending')) {
            $shiftStatusText = '提出済み';
        } elseif ($shiftRequests->contains('status', 'rejected')) {
            $shiftStatusText = '却下';
        } else {
            $shiftStatusText = '未提出';
        }

        return view('shift.index', compact(
            'year',
            'month',
            'shiftRequests',
            'shiftStatusText'
        ));
    }

    public function store(Request $request)
    {
        $userId = session('userId');

        if (!$userId) {
            return redirect('/login')->with('error', 'ログインしてください。');
        }

        $shifts = json_decode($request->shifts_json, true);

        if (empty($shifts)) {
            return back()->with('success', 'シフトが選択されていません。');
        }

        $year = $request->year;
        $month = $request->month;

        ShiftRequest::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->delete();

        foreach ($shifts as $shift) {
            ShiftRequest::create([
                'user_id'      => $userId,
                'request_type' => 'holiday',
                'work_date'    => $shift['work_date'],
                'remote'       => $shift['remote'],
                'start_time'   => $shift['start_time'],
                'end_time'     => $shift['end_time'],
                'reason'       => '',
                'status'       => 'pending',
            ]);
        }

        return redirect()
            ->route('shift.index', [
                'year' => $year,
                'month' => $month,
            ])
            ->with('success', 'シフトを保存しました。');
    }
}