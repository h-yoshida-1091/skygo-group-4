<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCharacter extends Model
{
    protected $table = 'user_characters';

    protected $fillable = [
        'user_id',
        'character_type',
        'nickname',
        'level',
        'exp',
        'title',
        'total_work_time',
        'login_count',
        'clock_in_voice',
        'clock_out_voice',
        'image',
    ];

    /**
     * このキャラクターを所有するユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}