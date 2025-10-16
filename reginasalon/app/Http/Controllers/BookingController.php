<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Store;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
                'bookingItems' => static function ($query) use ($startDate, $endDate): void {
                    $query->select(['id', 'booking_id', 'staff_id', 'service_id', 'start_time', 'duration'])
                        ->whereHas('booking', static function ($bookingQuery) use ($startDate, $endDate): void {
                            $bookingQuery->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
                                ->where('status', '!=', 'cancelled');
                        })
                        ->with([
                            'booking' => static function ($bookingQuery): void {
                                $bookingQuery->select(['id', 'booking_date', 'booking_time', 'status']);
                            },
                        ])
                        ->orderBy('booking_id')
                        ->orderBy('start_time');
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
                'bookings' => $staff->bookingItems
                    ->map(static function (BookingItem $bookingItem) {
                        $booking = $bookingItem->booking;

                        if (! $booking?->booking_date) {
                            return null;
                        }

                        $dateString = $booking->booking_date instanceof Carbon
                            ? $booking->booking_date->format('Y-m-d')
                            : Carbon::parse($booking->booking_date)->format('Y-m-d');

                        $startTime = $bookingItem->start_time;
                        $timeString = null;

                        if ($startTime) {
                            try {
                                $timeString = Carbon::createFromFormat('H:i:s', $startTime)->format('H:i');
                            } catch (\InvalidArgumentException $exception) {
                                $timeString = Carbon::parse($startTime)->format('H:i');
                            }
                        }

                        if (! $timeString) {
                            $timeString = $booking->booking_time instanceof Carbon
                                ? $booking->booking_time->format('H:i')
                                : Carbon::parse($booking->booking_time)->format('H:i');
                        }

                        $duration = max(1, (int) $bookingItem->duration);

                        return [
                            'date' => $dateString,
                            'start_time' => $timeString,
                            'duration' => $duration,
                        ];
                    })
                    ->filter()
                    ->sortBy(static fn($slot) => $slot['date'] . ' ' . $slot['start_time'])
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

        $serviceOrder = collect($validated['services'])
            ->map(static fn($id) => (int) $id)
            ->filter()
            ->values();

        $serviceIds = $serviceOrder
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
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $bookingDate . ' ' . $bookingTime, config('app.timezone'));
        $now = Carbon::now(config('app.timezone'));

        if ($startDateTime->isSameDay($now)) {
            $cutoff = $now->copy()->setTime(15, 0, 0);

            if ($now->greaterThanOrEqualTo($cutoff)) {
                throw ValidationException::withMessages([
                    'booking_date' => 'Booking untuk tanggal hari ini hanya dapat dilakukan sebelum pukul 15:00.',
                ]);
            }

            $nextSlotMinutes = (int) ceil((($now->hour * 60) + $now->minute) / 30) * 30;
            $nextSlotHour = intdiv($nextSlotMinutes, 60);
            $nextSlotMinute = $nextSlotMinutes % 60;
            $nextAvailableStart = $now->copy()->setTime($nextSlotHour, $nextSlotMinute, 0);

            if ($startDateTime->lessThan($nextAvailableStart)) {
                throw ValidationException::withMessages([
                    'booking_time' => 'Untuk booking di tanggal hari ini, pilih jam setelah waktu saat ini.',
                ]);
            }
        }

        $initialStartDateTime = CarbonImmutable::createFromFormat(
            'Y-m-d H:i',
            $bookingDate . ' ' . $bookingTime,
            config('app.timezone')
        );

        $servicePlans = [];
        $currentStartDateTime = $initialStartDateTime;

        foreach ($serviceOrder as $serviceId) {
            $staffId = $assignments->get($serviceId);

            if (! $staffId) {
                throw ValidationException::withMessages([
                    'assignments' => 'Setiap layanan harus memiliki stylist yang ditentukan.',
                ]);
            }

            $service = $services->get($serviceId);

            if (! $service) {
                throw ValidationException::withMessages([
                    'services' => 'Layanan yang dipilih tidak tersedia.',
                ]);
            }

            $serviceDuration = max(1, (int) $service->duration);
            $startDateTimeForService = $currentStartDateTime;
            $endDateTimeForService = $startDateTimeForService->addMinutes($serviceDuration);

            $servicePlans[] = [
                'service' => $service,
                'staff_id' => $staffId,
                'duration' => $serviceDuration,
                'start' => $startDateTimeForService,
                'end' => $endDateTimeForService,
            ];

            $currentStartDateTime = $endDateTimeForService;
        }

        $createdBooking = DB::transaction(function () use ($servicePlans, $store, $user, $validated, $initialStartDateTime) {
            if ($user) {
                $user->forceFill([
                    'name' => $validated['customer_name'] ?: $user->name,
                    'phone' => $validated['customer_phone'],
                ])->save();
            }

            foreach ($servicePlans as $plan) {
                /** @var \App\Models\Service $service */
                $service = $plan['service'];
                $staffId = $plan['staff_id'];
                $startDateTime = $plan['start'];
                $endDateTime = $plan['end'];

                $bookingDateForService = $startDateTime->toDateString();

                $existingItems = BookingItem::query()
                    ->where('staff_id', $staffId)
                    ->whereHas('booking', static function ($query) use ($bookingDateForService): void {
                        $query->whereDate('booking_date', $bookingDateForService)
                            ->where('status', '!=', 'cancelled');
                    })
                    ->with(['booking:id,booking_date'])
                    ->lockForUpdate()
                    ->get();

                foreach ($existingItems as $existingItem) {
                    $booking = $existingItem->booking;

                    if (! $booking?->booking_date) {
                        continue;
                    }

                    $existingStart = $booking->booking_date instanceof Carbon
                        ? $booking->booking_date->copy()->setTimeFromTimeString($existingItem->start_time)
                        : Carbon::parse($booking->booking_date . ' ' . $existingItem->start_time);

                    $existingDuration = max(1, (int) $existingItem->duration);
                    $existingEnd = (clone $existingStart)->addMinutes($existingDuration);

                    if ($startDateTime->lt($existingEnd) && $endDateTime->gt($existingStart)) {
                        throw ValidationException::withMessages([
                            'booking_time' => 'Stylist yang dipilih sudah memiliki booking pada waktu tersebut.',
                        ]);
                    }
                }
            }


            $booking = Booking::query()->create([
                'user_id' => $user?->id,
                'store_id' => $store->id,
                'booking_date' => $initialStartDateTime->toDateString(),
                'booking_time' => $initialStartDateTime->format('H:i:s'),
                'staff_id' => null,
                'status' => 'pending',
            ]);

            foreach ($servicePlans as $plan) {
                /** @var \App\Models\Service $service */
                $service = $plan['service'];

                BookingItem::query()->create([
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'staff_id' => (int) $plan['staff_id'],
                    'start_time' => $plan['start']->format('H:i:s'),
                    'duration' => $plan['duration'],
                    'price' => $service->price,
                ]);
            }

            return $booking->fresh(['items']);
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
                'booking_id' => $createdBooking->id,
                'booking_ids' => [$createdBooking->id],
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
            'booking_id' => $createdBooking->id,
            'booking_ids' => collect([$createdBooking->id])->values(),
        ], 201);
    }
}
