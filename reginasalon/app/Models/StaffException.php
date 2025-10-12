<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffException extends Model
{
    use HasFactory;

    protected $table = 'staff_exceptions';

    protected $fillable = [
        'staff_id',
        'date',
        'is_available',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date'        => 'date',
        'is_available' => 'boolean',
        // simpan time sebagai string atau ubah ke 'datetime:H:i' jika ingin objek Carbon
        // 'start_time'  => 'datetime:H:i',
        // 'end_time'    => 'datetime:H:i',
    ];

    /* =======================
     | RELATIONS
     ======================= */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
