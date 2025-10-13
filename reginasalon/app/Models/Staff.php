<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'phone',
        'image',
    ];


    /* =======================
     | RELATIONS
     ======================= */

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'staff_services')
            ->withTimestamps()
            ->withPivot('id');
    }


    public function schedules()
    {
        return $this->hasMany(StaffWeeklySchedule::class);
    }

    public function exceptions()
    {
        return $this->hasMany(StaffException::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
