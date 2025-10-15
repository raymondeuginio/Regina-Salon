<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'booking_date',
        'booking_time',
        'staff_id',
        'staff_ids',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime:H:i',
        'staff_ids' => 'array',
    ];

    /**
     * RELATION
     */
    // Booking milik seorang user (pelanggan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Booking terjadi di satu store
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Booking bisa punya 1 staff (nullable)
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // Booking memiliki beberapa item layanan (service yang dibooking)
    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    // Booking punya satu payment (karena pembayaran per booking)
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
