<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffWeeklySchedule extends Model
{
    use HasFactory;

    protected $table = 'staff_weekly_schedule';

    protected $fillable = [
        'staff_id',
        'day_of_week', // 0 = Sunday ... 6 = Saturday
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        // 'start_time' => 'datetime:H:i',
        // 'end_time'   => 'datetime:H:i',
    ];

    /* =======================
     | RELATIONS
     ======================= */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
