<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Staff;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display the booking flow once the customer has selected services.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $serviceIds = collect($request->input('services', []))
            ->map(static fn($id) => (int) $id)
            ->filter();

        if ($serviceIds->isEmpty()) {
            return redirect()->route('services.index');
        }

        $storeId = (int) $request->integer('store_id');

        $services = Service::query()
            ->select([
                'id',
                'store_id',
                'service_category_id',
                'name',
                'description',
                'duration',
                'price',
            ])
            ->with(['category:id,name'])
            ->whereIn('id', $serviceIds)
            ->when($storeId, static function ($query) use ($storeId): void {
                $query->where('store_id', $storeId);
            })
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            return redirect()->route('services.index');
        }

        $storeId = $storeId ?: (int) $services->first()->store_id;

        if ($services->pluck('store_id')->filter()->unique()->count() > 1) {
            return redirect()->route('services.index');
        }

        $store = Store::query()
            ->select(['id', 'name', 'address', 'district', 'city'])
            ->find($storeId);

        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addMonths(2)->endOfDay();

        $staffMembers = Staff::query()
            ->select(['id', 'store_id', 'name', 'image'])
            ->with([
                'services' => static function ($query): void {
                    $query->select(['services.id', 'services.name', 'services.duration']);
                },
                'schedules' => static function ($query): void {
                    $query->select(['id', 'staff_id', 'day_of_week', 'start_time', 'end_time'])
                        ->orderBy('day_of_week');
                },
                'bookings' => static function ($query) use ($startDate, $endDate): void {
                    $query->select(['id', 'staff_id', 'booking_date', 'booking_time'])
                        ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
                        ->with(['items:id,booking_id,duration'])
                        ->orderBy('booking_date')
                        ->orderBy('booking_time');
                },
            ])
            ->where('store_id', $storeId)
            ->orderBy('name')
            ->get();

        $staffPayload = $staffMembers->map(function (Staff $staff): array {
            $services = $staff->services->map(fn(Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'duration' => $service->duration,
            ]);

            $specialisations = $services->pluck('name')
                ->map(static fn($name) => preg_replace('/\s+\(.+\)$/', '', $name))
                ->unique()
                ->values()
                ->all();

            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'photo' => $staff->image ?: 'https://i.pravatar.cc/150?u=' . $staff->id,
                'rating' => number_format(4.2 + ((int) $staff->id % 6) / 10, 1),
                'bio' => 'Stylist profesional Regina Salon dengan pengalaman lebih dari 5 tahun dalam perawatan rambut dan kecantikan.',
                'specialisations' => array_slice($specialisations, 0, 3),
                'service_ids' => $services->pluck('id')->values()->all(),
                'services' => $services->values()->all(),
                'schedules' => $staff->schedules->map(static fn($schedule) => [
                    'day_of_week' => (int) $schedule->day_of_week,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ])->values()->all(),
                'bookings' => $staff->bookings
                    ->map(static function ($booking) {
                        if (! $booking->booking_date || ! $booking->booking_time) {
                            return null;
                        }

                        $dateString = $booking->booking_date instanceof Carbon
                            ? $booking->booking_date->format('Y-m-d')
                            : Carbon::parse($booking->booking_date)->format('Y-m-d');

                        $timeString = $booking->booking_time instanceof Carbon
                            ? $booking->booking_time->format('H:i')
                            : Carbon::parse($booking->booking_time)->format('H:i');

                        $duration = max(30, (int) $booking->items->sum('duration'));

                        return [
                            'date' => $dateString,
                            'start_time' => $timeString,
                            'duration' => $duration,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all(),
            ];
        })->values();

        $customer = [
            'name' => $request->user()?->name ?? '',
            'email' => $request->user()?->email ?? '',
            'phone' => (string) data_get($request->user(), 'phone', ''),
        ];

        $storeAddress = collect([
            optional($store)->address,
            optional($store)->district,
            optional($store)->city,
        ])->filter()->implode(', ');

        return view('booking.index', [
            'store' => [
                'id' => $storeId,
                'name' => optional($store)->name ?? 'Taman Palem',
                'address' => $storeAddress ?: 'Jl. Sunset Avenue No. 10, Cengkareng, Jakarta Barat',
            ],
            'services' => $services->map(fn(Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'duration' => $service->duration,
                'price' => $service->price,
                'description' => $service->description,
                'category' => $service->category?->name,
            ])->values(),
            'staff' => $staffPayload,
            'customer' => $customer,
        ]);
    }
}
