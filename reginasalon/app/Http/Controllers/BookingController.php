<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Notifications\BookingPendingNotification;
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

    /**
     * Store a new booking and lock the selected staff schedule.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['integer'],
            'assignments' => ['required', 'array'],
            'assignments.*' => ['required', 'integer', Rule::exists('staff', 'id')],
            'payment_method' => ['required', Rule::in(['pay_at_salon'])],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $serviceIds = collect($validated['services'])
            ->map(static fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($serviceIds->isEmpty()) {
            throw ValidationException::withMessages([
                'services' => 'Minimal satu layanan harus dipilih.',
            ]);
        }

        $assignments = collect($validated['assignments'] ?? [])
            ->mapWithKeys(static function ($staffId, $serviceId): array {
                return [(int) $serviceId => (int) $staffId];
            });

        if ($assignments->count() !== $serviceIds->count()) {
            throw ValidationException::withMessages([
                'assignments' => 'Setiap layanan harus memiliki stylist yang ditentukan.',
            ]);
        }

        $store = Store::query()->findOrFail((int) $validated['store_id']);

        $services = Service::query()
            ->whereIn('id', $serviceIds)
            ->where('store_id', $store->id)
            ->get()
            ->keyBy('id');

        if ($services->count() !== $serviceIds->count()) {
            throw ValidationException::withMessages([
                'services' => 'Layanan yang dipilih tidak tersedia.',
            ]);
        }

        $staffIds = $assignments->values()->unique()->values();

        $staffMembers = Staff::query()
            ->whereIn('id', $staffIds)
            ->where('store_id', $store->id)
            ->with(['services:id'])
            ->get()
            ->keyBy('id');

        if ($staffMembers->count() !== $staffIds->count()) {
            throw ValidationException::withMessages([
                'assignments' => 'Stylist yang dipilih tidak valid untuk store ini.',
            ]);
        }

        foreach ($assignments as $serviceId => $staffId) {
            $staff = $staffMembers->get($staffId);
            $service = $services->get($serviceId);

            if (! $staff || ! $service) {
                throw ValidationException::withMessages([
                    'assignments' => 'Pilihan stylist tidak valid.',
                ]);
            }

            $canHandleService = $staff->services->pluck('id')->contains($serviceId);

            if (! $canHandleService) {
                throw ValidationException::withMessages([
                    'assignments.' . $serviceId => sprintf('Stylist %s tidak dapat menangani layanan %s.', $staff->name, $service->name),
                ]);
            }
        }

        $bookingDate = Carbon::parse($validated['booking_date'])->toDateString();
        $bookingTime = $validated['booking_time'];

        $groupedAssignments = [];

        foreach ($assignments as $serviceId => $staffId) {
            $groupedAssignments[$staffId] ??= [];
            $groupedAssignments[$staffId][] = $serviceId;
        }

        $createdBookings = DB::transaction(function () use ($groupedAssignments, $services, $store, $bookingDate, $bookingTime, $user, $validated): array {
            if ($user) {
                $user->forceFill([
                    'name' => $validated['customer_name'] ?: $user->name,
                    'phone' => $validated['customer_phone'],
                ])->save();
            }

            $created = [];

            foreach ($groupedAssignments as $staffId => $serviceIdList) {
                $serviceCollection = collect($serviceIdList)
                    ->map(fn($id) => (int) $id)
                    ->filter()
                    ->map(fn($id) => $services->get($id))
                    ->filter();

                if ($serviceCollection->isEmpty()) {
                    throw ValidationException::withMessages([
                        'services' => 'Tidak dapat membuat booking tanpa layanan yang valid.',
                    ]);
                }

                $durationMinutes = max(30, (int) $serviceCollection->sum('duration'));

                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $bookingDate . ' ' . $bookingTime);
                $endDateTime = (clone $startDateTime)->addMinutes($durationMinutes);

                $existingBookings = Booking::query()
                    ->where('staff_id', $staffId)
                    ->whereDate('booking_date', $bookingDate)
                    ->where('status', '!=', 'cancelled')
                    ->with(['items:id,booking_id,duration'])
                    ->lockForUpdate()
                    ->get();

                foreach ($existingBookings as $existingBooking) {
                    $existingStart = $existingBooking->booking_date instanceof Carbon
                        ? $existingBooking->booking_date->copy()->setTimeFromTimeString($existingBooking->booking_time)
                        : Carbon::parse($existingBooking->booking_date . ' ' . $existingBooking->booking_time);

                    $existingDuration = max(30, (int) $existingBooking->items->sum('duration'));
                    $existingEnd = (clone $existingStart)->addMinutes($existingDuration);

                    if ($startDateTime->lt($existingEnd) && $endDateTime->gt($existingStart)) {
                        throw ValidationException::withMessages([
                            'booking_time' => 'Stylist yang dipilih sudah memiliki booking pada waktu tersebut.',
                        ]);
                    }
                }

                $booking = Booking::query()->create([
                    'user_id' => $user?->id,
                    'store_id' => $store->id,
                    'booking_date' => $bookingDate,
                    'booking_time' => $startDateTime->format('H:i'),
                    'staff_id' => (int) $staffId,
                    'status' => 'pending',
                ]);

                foreach ($serviceCollection as $service) {
                    BookingItem::query()->create([
                        'booking_id' => $booking->id,
                        'service_id' => $service->id,
                        'duration' => (int) $service->duration,
                        'price' => $service->price,
                    ]);
                }

                $created[] = $booking;
            }

            return $created;
        });

        $serviceSummary = $serviceIds->map(function ($serviceId) use ($services, $assignments, $staffMembers) {
            $service = $services->get($serviceId);
            $staffId = $assignments->get($serviceId);
            $staffName = $staffMembers->get($staffId)?->name;

            return [
                'name' => $service?->name,
                'duration' => (int) $service?->duration,
                'price' => $service?->price,
                'staff' => $staffName,
            ];
        })->filter(fn($service) => $service['name'])->values();

        $totalDuration = (int) $serviceSummary->sum('duration');
        $totalPrice = (float) $serviceSummary->sum('price');

        $customerName = $validated['customer_name'] ?: ($user?->name ?? 'Pelanggan Regina Salon');
        $emailAddress = $validated['customer_email'] ?: ($user?->email ?? null);

        if ($emailAddress) {
            Notification::route('mail', $emailAddress)->notify(new BookingPendingNotification([
                'customer_name' => $customerName,
                'booking_date' => Carbon::parse($bookingDate)->translatedFormat('l, d F Y'),
                'booking_time' => Carbon::createFromFormat('H:i', $bookingTime)->format('H:i'),
                'store' => [
                    'name' => $store->name,
                    'address' => collect([
                        $store->address,
                        $store->district,
                        $store->city,
                    ])->filter()->implode(', '),
                ],
                'services' => $serviceSummary->map(function ($service) {
                    return [
                        'name' => $service['name'],
                        'duration' => $service['duration'],
                        'staff' => $service['staff'],
                    ];
                })->all(),
                'total_duration' => $totalDuration,
                'total_price' => $totalPrice,
                'notes' => $validated['notes'] ?? null,
            ]));
        }

        return response()->json([
            'message' => 'Booking berhasil disimpan dan sedang menunggu konfirmasi.',
            'booking_ids' => collect($createdBookings)->map(fn(Booking $booking) => $booking->id)->values(),
        ], 201);
    }
}
