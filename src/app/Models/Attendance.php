<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function getStartTimeFormattedAttribute()
    {
        return $this->start_time
            ? \Carbon\Carbon::parse($this->start_time)->format('H:i')
            : '';
    }

    public function getEndTimeFormattedAttribute()
    {
        return $this->end_time
            ? \Carbon\Carbon::parse($this->end_time)->format('H:i')
            : '';
    }

    public function request()
    {
        return $this->hasOne(StampCorrectionRequest::class);
    }
    
}
