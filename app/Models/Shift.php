<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'work_date',
        'remote',
        'start_time',
        'end_time',
        'break_time',
    ];

    protected $casts = [
        'work_date' => 'date',
        'remote' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shiftRequests()
    {
        return $this->hasMany(ShiftRequest::class, 'shift_id');
    }
}