<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftRequest;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $userId = session('userId');

        $shifts = Shift::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->get();

        $pendingSubmitRequests = ShiftRequest::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->where('request_type', 'submit')
            ->where('status', 'pending')
            ->get();

        $pendingChangeRequests = ShiftRequest::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->where('request_type', 'change')
            ->where('status', 'pending')
            ->get();

        if ($pendingChangeRequests->isNotEmpty()) {
            $shiftStatusText = '変更申請中';
            $pendingRequests = $pendingChangeRequests;
        } elseif ($pendingSubmitRequests->isNotEmpty()) {
            $shiftStatusText = '申請中';
            $pendingRequests = $pendingSubmitRequests;
        } elseif ($shifts->isNotEmpty()) {
            $shiftStatusText = '提出済み';
            $pendingRequests = collect();
        } else {
            $shiftStatusText = '未提出';
            $pendingRequests = collect();
        }

        return view('shift.index', compact(
            'year',
            'month',
            'shifts',
            'pendingRequests',
            'shiftStatusText'
        ));
    }

    public function store(Request $request)
    {
        $userId = session('userId');

        if (!$userId) {
            return redirect('/login')->with('error', 'ログインしてください。');
        }

        $year = $request->year;
        $month = $request->month;
        $shiftsJson = json_decode($request->shifts_json, true);

        if (empty($shiftsJson)) {
            return back()->with('success', 'シフトが選択されていません。');
        }

        $hasConfirmedShift = Shift::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->exists();

        $hasPendingChange = ShiftRequest::where('user_id', $userId)
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->where('request_type', 'change')
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingChange) {
            return redirect()
                ->route('shift.index', ['year' => $year, 'month' => $month])
                ->with('success', '変更申請中のため、新しい申請はできません。');
        }

        $requestType = $hasConfirmedShift ? 'change' : 'submit';

        // 初回申請中は何度でも修正できるように、古いsubmit pendingを置き換える
        if ($requestType === 'submit') {
            ShiftRequest::where('user_id', $userId)
                ->whereYear('work_date', $year)
                ->whereMonth('work_date', $month)
                ->where('request_type', 'submit')
                ->where('status', 'pending')
                ->delete();
        }

        foreach ($shiftsJson as $shift) {
            $confirmedShift = null;

            if ($requestType === 'change') {
                $confirmedShift = Shift::where('user_id', $userId)
                    ->where('work_date', $shift['work_date'])
                    ->first();
            }

            ShiftRequest::create([
                'user_id'      => $userId,
                'shift_id'     => $confirmedShift ? $confirmedShift->id : null,
                'request_type' => $requestType,
                'work_date'    => $shift['work_date'],
                'remote'       => $shift['remote'],
                'start_time'   => $shift['start_time'],
                'end_time'     => $shift['end_time'],
                'reason'       => null,
                'status'       => 'pending',
                'submitted_at' => now(),
            ]);
        }

        $message = $requestType === 'submit'
            ? 'シフトを申請しました。'
            : 'シフト変更申請を提出しました。';

        return redirect()
            ->route('shift.index', ['year' => $year, 'month' => $month])
            ->with('success', $message);
    }
}