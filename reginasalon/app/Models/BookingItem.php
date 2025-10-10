<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use HasFactory;

    protected $table = 'booking_items';

    protected $fillable = [
        'booking_id',
        'service_id',
        'duration',
        'price',
    ];

    protected $casts = [
        'duration' => 'integer',
        'price' => 'decimal:2',
    ];

    /* =======================
     | RELATIONS
     ======================= */

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
