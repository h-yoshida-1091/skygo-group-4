<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftRequest extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'request_type',
        'work_date',
        'remote',
        'start_time',
        'end_time',
        'reason',
        'status',
    ];

    protected $casts = [
        'work_date' => 'date',
        'remote' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}