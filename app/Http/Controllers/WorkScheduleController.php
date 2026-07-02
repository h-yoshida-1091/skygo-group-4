<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Shift;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('userId');

        $tab = $request->query('tab', 'main');
        $monthParam = $request->query('month', Carbon::now()->format('Y-m'));

        try {
            $currentMonth = Carbon::parse($monthParam);
        } catch (\Exception $e) {
            $currentMonth = Carbon::now();
        }

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $attendances = collect();
        $shifts = collect();

        if ($userId) {
            $attendances = Attendance::where('user_id', $userId)
                ->whereBetween('work_date', [
                    $startOfMonth->format('Y-m-d'),
                    $endOfMonth->format('Y-m-d')
                ])
                ->get()
                ->keyBy(function ($attendance) {
                    return $attendance->work_date->format('Y-m-d');
                });

            $shifts = Shift::where('user_id', $userId)
                ->whereBetween('work_date', [
                    $startOfMonth->format('Y-m-d'),
                    $endOfMonth->format('Y-m-d')
                ])
                ->get()
                ->keyBy(function ($shift) {
                    return $shift->work_date->format('Y-m-d');
                });
        }

        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];

        $workSchedule = [];

        $totalWorkMinutes = 0;
        $prescribedWorkMinutes = 0;
        $workDays = 0;
        $totalBreakMinutes = 0;
        $lateCount = 0;
        $absentDays = 0;

        $currentDay = $startOfMonth->copy();

        while ($currentDay <= $endOfMonth) {
            $dateKey = $currentDay->format('Y-m-d');

            $attendance = $attendances->get($dateKey);
            $shift = $shifts->get($dateKey);

            $clockInText = '';
            $clockOutText = '';
            $breakText = '';
            $lateText = '';
            $totalWorkText = '';

            $breakMinutes = 60;

            if ($shift) {
                $shiftStart = Carbon::parse($dateKey . ' ' . $shift->start_time);
                $shiftEnd = Carbon::parse($dateKey . ' ' . $shift->end_time);

                $prescribedMinutes = $shiftStart->diffInMinutes($shiftEnd) - $breakMinutes;

                if ($prescribedMinutes > 0) {
                    $prescribedWorkMinutes += $prescribedMinutes;
                }
            }

            if ($attendance && $attendance->clock_in) {
                $clockIn = Carbon::parse($attendance->clock_in);
                $clockInText = $clockIn->format('H:i');

                if ($shift) {
                    $shiftStart = Carbon::parse($dateKey . ' ' . $shift->start_time);

                    if ($clockIn->gt($shiftStart)) {
                        $lateCount++;
                        $lateText = '1';
                    } else {
                        $lateText = '0';
                    }
                }
            }

            if ($attendance && $attendance->clock_out) {
                $clockOut = Carbon::parse($attendance->clock_out);
                $clockOutText = $clockOut->format('H:i');
            }

            if ($attendance && $attendance->clock_in && $attendance->clock_out) {
                $clockIn = Carbon::parse($attendance->clock_in);
                $clockOut = Carbon::parse($attendance->clock_out);

                $workDays++;

                $breakText = '60分';
                $totalBreakMinutes += $breakMinutes;

                $workMinutes = $clockIn->diffInMinutes($clockOut) - $breakMinutes;

                if ($workMinutes > 0) {
                    $totalWorkMinutes += $workMinutes;
                    $totalWorkText = $this->formatMinutes($workMinutes);
                }
            }

            if ($shift && !$attendance) {
                $absentDays++;
            }

            $workSchedule[] = [
                'date' => $currentDay->format('n/j'),
                'day_of_week' => $weekDays[$currentDay->dayOfWeek],
                'clock_in' => $clockInText,
                'clock_out' => $clockOutText,
                'break_time' => $breakText,
                'late_count' => $lateText,
                'total_work_time' => $totalWorkText,
            ];

            $currentDay->addDay();
        }

        $summary = [
            'total_work' => $this->formatMinutes($totalWorkMinutes),
            'prescribed_work' => $this->formatMinutes($prescribedWorkMinutes),
            'work_days' => $workDays . '日',
            'total_break' => $this->formatMinutes($totalBreakMinutes),
            'late_early' => $lateCount . '回',
            'absent_days' => $absentDays . '日',
        ];

        return view('workSchedule', [
            'tab' => $tab,
            'currentMonth' => $currentMonth->format('Y-m'),
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'workSchedule' => $workSchedule,
            'summary' => $summary,
        ]);
    }

    private function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%d:%02d', $hours, $mins);
    }
}