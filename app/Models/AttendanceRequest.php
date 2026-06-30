<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'work_date',
        'requested_clock_in',
        'requested_clock_out',
        'reason',
        'status',
    ];

    protected $casts = [
        'work_date' => 'date',
        'requested_clock_in' => 'datetime',
        'requested_clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}