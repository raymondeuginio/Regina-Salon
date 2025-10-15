<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Mail\BookingReminderMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    /**
     * Display the booking flow once the customer has selected services.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $serviceIds = collect($request->input('services', []))
            ->map(static fn ($id) => (int) $id)
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
                    $query->select([
                        'services.id',
                        'services.service_category_id',
                        'services.name',
                        'services.duration',
                    ]);
                },
                'schedules' => static function ($query): void {
                    $query->select(['id', 'staff_id', 'day_of_week', 'start_time', 'end_time'])
                        ->orderBy('day_of_week');
                },
            ])
            ->where('store_id', $storeId)
            ->orderBy('name')
            ->get();

        $storeBookings = Booking::query()
            ->select(['id', 'booking_date', 'booking_time', 'staff_id', 'staff_ids', 'status'])
            ->where('store_id', $storeId)
            ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->with(['items:id,booking_id,duration'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();

        $bookingsByStaff = [];

        foreach ($storeBookings as $booking) {
            $relatedStaff = collect($booking->staff_ids ?? [])
                ->map(static fn ($id) => (int) $id);

            if ($booking->staff_id) {
                $relatedStaff->push((int) $booking->staff_id);
            }

            foreach ($relatedStaff->unique() as $staffId) {
                $bookingsByStaff[$staffId] ??= collect();
                $bookingsByStaff[$staffId] = $bookingsByStaff[$staffId]->push($booking);
            }
        }

        $staffPayload = $staffMembers->map(function (Staff $staff): array {
            $services = $staff->services->map(fn (Service $service) => [
                'id' => $service->id,
                'category_id' => $service->service_category_id,
                'name' => $service->name,
                'duration' => $service->duration,
            ]);

            $specialisations = $services->pluck('name')
                ->map(static fn ($name) => preg_replace('/\s+\(.+\)$/', '', $name))
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
                'schedules' => $staff->schedules->map(static fn ($schedule) => [
                    'day_of_week' => (int) $schedule->day_of_week,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ])->values()->all(),
                'bookings' => collect($bookingsByStaff[$staff->id] ?? [])
                    ->map(static function (Booking $booking) {
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
            'services' => $services->map(fn (Service $service) => [
                'id' => $service->id,
                'category_id' => $service->service_category_id,
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

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['integer', 'exists:services,id'],
            'staff_assignments' => ['required', 'array'],
            'staff_assignments.*' => ['nullable', 'integer', 'exists:staff,id'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'payment_method' => ['required', Rule::in(['pay_at_salon'])],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'customer_email.required' => 'Alamat email wajib diisi untuk menerima pengingat.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = $request->user();

        $customerPhone = trim((string) ($user?->phone ?? ''));
        $sanitisedPhone = preg_replace('/[^0-9+]/', '', $customerPhone);

        if ($sanitisedPhone === '' || ! preg_match('/^(\+?\d{9,15})$/', $sanitisedPhone)) {
            return response()->json([
                'message' => 'Nomor WhatsApp pada akun Anda belum valid.',
                'errors' => [
                    'customer_phone' => ['Perbarui nomor WhatsApp di profil Anda sebelum melakukan booking.'],
                ],
            ], 422);
        }

        $normalisedPhone = str_starts_with($customerPhone, '+')
            ? '+' . ltrim($sanitisedPhone, '+')
            : $sanitisedPhone;

        $storeId = (int) $validated['store_id'];
        $serviceIds = collect($validated['services'])->map(static fn ($id) => (int) $id)->unique()->values();
        $assignments = collect($validated['staff_assignments'])
            ->map(static fn ($id) => $id !== null ? (int) $id : null);

        $missingAssignments = $serviceIds->filter(static fn ($serviceId) => ! $assignments->has($serviceId) || ! $assignments->get($serviceId));

        if ($missingAssignments->isNotEmpty()) {
            return response()->json([
                'message' => 'Pilih staff untuk setiap layanan yang dibooking.',
                'errors' => ['staff_assignments' => ['Staff untuk layanan tertentu belum dipilih.']],
            ], 422);
        }

        $staffIds = $assignments->values()->filter()->unique()->values();

        if ($staffIds->isEmpty()) {
            return response()->json([
                'message' => 'Pilih minimal satu staff untuk melanjutkan.',
            ], 422);
        }

        $services = Service::query()
            ->select(['id', 'store_id', 'name', 'duration', 'price'])
            ->whereIn('id', $serviceIds)
            ->where('store_id', $storeId)
            ->get();

        if ($services->count() !== $serviceIds->count()) {
            return response()->json([
                'message' => 'Sebagian layanan tidak tersedia untuk store ini.',
            ], 422);
        }

        $staffMembers = Staff::query()
            ->select(['id', 'store_id'])
            ->with(['services:id'])
            ->whereIn('id', $staffIds)
            ->where('store_id', $storeId)
            ->get()
            ->keyBy('id');

        if ($staffMembers->count() !== $staffIds->count()) {
            return response()->json([
                'message' => 'Sebagian staff tidak tersedia di store ini.',
            ], 422);
        }

        foreach ($serviceIds as $serviceId) {
            $staffId = $assignments->get($serviceId);

            if (! $staffMembers->has($staffId)) {
                return response()->json([
                    'message' => 'Staff yang dipilih tidak valid.',
                ], 422);
            }

            $staff = $staffMembers->get($staffId);
            $eligible = $staff->services->contains('id', $serviceId);

            if (! $eligible) {
                return response()->json([
                    'message' => 'Staff yang dipilih tidak memiliki spesialisasi untuk layanan tertentu.',
                ], 422);
            }
        }

        $totalDuration = (int) max(30, $services->sum(static fn ($service) => (int) $service->duration));
        $bookingDate = Carbon::parse($validated['booking_date'])->startOfDay();
        $startTime = Carbon::createFromFormat('H:i', $validated['booking_time']);
        $startMinutes = $startTime->hour * 60 + $startTime->minute;
        $endMinutes = $startMinutes + $totalDuration;

        $store = Store::query()->select(['id', 'name', 'address', 'district', 'city'])->find($storeId);

        try {
            $booking = DB::transaction(function () use (
                $storeId,
                $user,
                $bookingDate,
                $startTime,
                $startMinutes,
                $endMinutes,
                $totalDuration,
                $services,
                $staffIds
            ) {
                $existingBookingsQuery = Booking::query()
                    ->where('store_id', $storeId)
                    ->whereDate('booking_date', $bookingDate)
                    ->whereIn('status', ['pending', 'confirmed', 'completed'])
                    ->with(['items:id,booking_id,duration'])
                    ->lockForUpdate();

                $existingBookingsQuery->where(function ($query) use ($staffIds): void {
                    $query->whereIn('staff_id', $staffIds);

                    foreach ($staffIds as $staffId) {
                        $query->orWhereJsonContains('staff_ids', $staffId);
                    }
                });

                $existingBookings = $existingBookingsQuery->get();

                foreach ($existingBookings as $existing) {
                    $existingStart = Carbon::parse($existing->booking_time);
                    $existingStartMinutes = $existingStart->hour * 60 + $existingStart->minute;
                    $existingDuration = max(30, (int) $existing->items->sum('duration'));
                    $existingEndMinutes = $existingStartMinutes + $existingDuration;

                    if ($endMinutes > $existingStartMinutes && $startMinutes < $existingEndMinutes) {
                        throw ValidationException::withMessages([
                            'booking_time' => 'Slot waktu sudah terisi untuk salah satu staff yang dipilih.',
                        ]);
                    }
                }

                $booking = Booking::create([
                    'user_id' => $user?->id,
                    'store_id' => $storeId,
                    'booking_date' => $bookingDate,
                    'booking_time' => $startTime->format('H:i:s'),
                    'staff_id' => $staffIds->first(),
                    'staff_ids' => $staffIds->values()->all(),
                    'status' => 'pending',
                ]);

                foreach ($services as $service) {
                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'service_id' => $service->id,
                        'duration' => (int) $service->duration,
                        'price' => $service->price,
                    ]);
                }

                return $booking;
            });
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Tidak dapat membuat booking.',
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($user && filled($validated['customer_email']) && $user->email !== $validated['customer_email']) {
            $user->forceFill(['email' => $validated['customer_email']])->save();
        }

        $booking->load('items');

        $endTime = Carbon::createFromTime($startTime->hour, $startTime->minute)->addMinutes($totalDuration);
        $dateLabel = $bookingDate->copy()->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $timeLabel = $startTime->format('H:i') . ' - ' . $endTime->format('H:i');

        $emailSent = false;

        if ($validated['customer_email']) {
            try {
                Mail::to($validated['customer_email'])->send(new BookingReminderMail(
                    customerName: $validated['customer_name'],
                    storeName: $store?->name,
                    storeAddress: collect([
                        optional($store)->address,
                        optional($store)->district,
                        optional($store)->city,
                    ])->filter()->implode(', '),
                    dateLabel: $dateLabel,
                    timeLabel: $timeLabel,
                    services: $services->pluck('name')->all()
                ));

                $emailSent = true;
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'message' => 'Booking berhasil dibuat.',
            'booking' => [
                'id' => $booking->id,
                'booking_date' => $booking->booking_date?->format('Y-m-d'),
                'booking_time' => $booking->booking_time?->format('H:i'),
                'duration_minutes' => $totalDuration,
                'staff_ids' => $staffIds->values()->all(),
            ],
            'email_sent' => $emailSent,
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $normalisedPhone,
        ]);
    }
}
