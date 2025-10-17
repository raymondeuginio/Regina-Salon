<?php

namespace App\Models;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Filament\Notifications\Notification;


class Booking extends Model
{
    //
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'user_id',
        'store_id',
        'booking_date',
        'booking_time',
        'staff_id',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime:H:i:s'
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

    // Relasi ke services lewar booking_items
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_items')
            ->withPivot('duration', 'price') // Sesuai kolom di booking_items
            ->withTimestamps();
    }

    // hasMany jika ingin akses langsung ke booking_items
    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }


    //
    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->services->sum('pivot.price'),
        );
    }


    protected static function booted(): void
    {
        static::created(function ($booking) {
            $recipients = User::whereIn('role', ['admin', 'owner'])->get();

            foreach ($recipients as $user) {
                Notification::make()
                    ->title('New Booking Received')
                    ->body("From {$booking->user->name}. Booked on {$booking->booking_date} {$booking->booking_time}.")
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->actions([
                        Action::make('View')
                            ->url(BookingResource::getUrl('view', ['record' => $booking]))
                            ->button(),
                    ])
                    ->sendToDatabase($user);
            }
        });
    }
}
