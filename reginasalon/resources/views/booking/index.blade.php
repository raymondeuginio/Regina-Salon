@extends('layouts.public')

@section('title', 'Booking Regina Salon')

@section('content')
<section class="bg-white py-16">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-400">Booking</p>
                <h1 class="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">Atur Jadwal Kunjungan Anda</h1>
                <p class="mt-2 text-sm text-gray-500">Tinjau layanan yang dipilih, pilih stylist favorit, dan pastikan jadwalnya sesuai dengan kebutuhan Anda.</p>
            </div>
            <div class="rounded-3xl border border-rose-100 bg-rose-50 px-6 py-4 text-sm text-rose-500">
                <p class="font-semibold text-rose-600">{{ $store['name'] }}</p>
                <p class="mt-1 text-xs text-rose-400">{{ $store['address'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-white px-4 py-1 text-xs font-semibold text-rose-500">Store ID: {{ $store['id'] }}</span>
            </div>
        </header>

        <div class="mt-12 grid gap-10 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <form id="booking-form" class="space-y-10" action="{{ route('booking.store') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="store_id" value="{{ $store['id'] }}">

                <section class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Langkah 1</p>
                            <h2 class="mt-1 text-xl font-semibold text-gray-900">Review Layanan</h2>
                            <p class="text-sm text-gray-500">Pastikan daftar layanan sesuai. Anda masih bisa menghapus atau menambah layanan sebelum melanjutkan.</p>
                        </div>
                        <a href="{{ route('services.index', ['store_id' => $store['id']]) }}" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600 transition hover:border-rose-400 hover:text-rose-700">
                            Tambah Layanan
                        </a>
                    </div>

                    <div class="space-y-4" data-review-list></div>
                </section>

                <section class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Langkah 2</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">Pilih Staff / Stylist</h2>
                        <p class="text-sm text-gray-500">Kami menampilkan stylist yang dapat menangani setiap layanan yang Anda pilih. Silakan sesuaikan jika ingin memilih stylist berbeda.</p>
                    </div>

                    <div class="space-y-5" data-staff-per-service></div>
                </section>

                <section class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Langkah 3</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">Pilih Tanggal &amp; Waktu</h2>
                        <p class="text-sm text-gray-500">Gunakan kalender untuk memilih tanggal sesuai ketersediaan stylist. Slot waktu otomatis dinonaktifkan jika jadwal sudah penuh.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-3xl border border-rose-100">
                            <div class="flex items-center justify-between border-b border-rose-100 px-5 py-4">
                                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-rose-100 text-rose-500 transition hover:border-rose-400 hover:text-rose-600" data-calendar-prev>
                                    <span class="sr-only">Bulan sebelumnya</span>
                                    &larr;
                                </button>
                                <div class="text-sm font-semibold text-gray-900" data-calendar-title>Bulan</div>
                                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-rose-100 text-rose-500 transition hover:border-rose-400 hover:text-rose-600" data-calendar-next>
                                    <span class="sr-only">Bulan berikutnya</span>
                                    &rarr;
                                </button>
                            </div>
                            <div class="grid grid-cols-7 gap-2 px-5 pt-4 text-center text-xs font-semibold uppercase tracking-[0.3em] text-gray-400">
                                <span>Min</span>
                                <span>Sen</span>
                                <span>Sel</span>
                                <span>Rab</span>
                                <span>Kam</span>
                                <span>Jum</span>
                                <span>Sab</span>
                            </div>
                            <div class="grid grid-cols-7 gap-2 p-5" data-calendar-grid></div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Pilih waktu</p>
                            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3" data-time-options>
                                <p class="col-span-full rounded-2xl border border-dashed border-rose-200 bg-rose-50 px-4 py-3 text-center text-xs text-rose-500">Pilih tanggal terlebih dahulu untuk melihat slot waktu.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Langkah 4</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">Informasi Customer</h2>
                        <p class="text-sm text-gray-500">Data otomatis terisi dari akun Anda. Silakan periksa kembali atau lakukan penyesuaian bila diperlukan.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="text-sm font-medium text-gray-700">
                            Nama Lengkap
                            <input type="text" name="customer_name" class="mt-1 w-full rounded-2xl border border-rose-100 px-4 py-3 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-200" value="{{ $customer['name'] }}" placeholder="Nama lengkap Anda">
                        </label>
                        <label class="text-sm font-medium text-gray-700">
                            Email
                            <input type="email" name="customer_email" class="mt-1 w-full rounded-2xl border border-rose-100 px-4 py-3 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-200" value="{{ $customer['email'] }}" placeholder="Email aktif">
                        </label>
                        <label class="text-sm font-medium text-gray-700 sm:col-span-2">
                            Nomor WhatsApp
                            <input type="tel" name="customer_phone" required class="mt-1 w-full rounded-2xl border border-rose-100 px-4 py-3 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-200" value="{{ $customer['phone'] }}" placeholder="08xxxxxxxxxx">
                        </label>
                        <label class="text-sm font-medium text-gray-700 sm:col-span-2">
                            Catatan Tambahan
                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-rose-100 px-4 py-3 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-200" placeholder="Catat permintaan khusus atau kondisi rambut/kulit Anda"></textarea>
                        </label>
                    </div>
                </section>

                <section class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Langkah 5</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">Pembayaran</h2>
                        <p class="text-sm text-gray-500">Pilih metode pembayaran yang tersedia untuk booking Anda.</p>
                    </div>
                    <label class="flex items-start gap-3 rounded-2xl border border-rose-100 px-4 py-3 text-sm text-gray-600">
                        <input type="radio" name="payment_method" value="pay_at_salon" class="mt-1">
                        <span>
                            <span class="block font-semibold text-gray-900">Pay at Salon</span>
                            <span class="text-xs text-gray-500">Bayar langsung di lokasi saat kedatangan.</span>
                        </span>
                    </label>
                </section>
            </form>

            <aside class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm" data-booking-summary>
                <div class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" data-confirmation-message>
                    <p class="font-semibold">Booking berhasil dibuat!</p>
                    <p class="mt-1 text-xs">Kami telah mengirimkan ringkasan ke email Anda. Nantikan pengingat jadwal dan detail booking melalui email.</p>
                </div>

                <div class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600" data-error-message></div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Ringkasan Booking</p>
                    <h3 class="mt-2 text-xl font-semibold text-gray-900">Detail pilihan Anda</h3>
                </div>

                <div class="space-y-4 text-sm" data-summary-services></div>

                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between text-gray-500">
                        <dt>Durasi Total</dt>
                        <dd class="font-semibold text-gray-900" data-summary-duration>-</dd>
                    </div>
                    <div class="flex items-center justify-between text-gray-500">
                        <dt>Stylist</dt>
                        <dd class="font-semibold text-gray-900" data-summary-staff>-</dd>
                    </div>
                    <div class="flex items-center justify-between text-gray-500">
                        <dt>Tanggal &amp; Waktu</dt>
                        <dd class="font-semibold text-gray-900" data-summary-datetime>-</dd>
                    </div>
                    <div class="flex items-center justify-between text-gray-500">
                        <dt>Metode Pembayaran</dt>
                        <dd class="font-semibold text-gray-900" data-summary-payment>-</dd>
                    </div>
                </dl>

                <div class="flex items-center justify-between rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600">
                    <span>Total</span>
                    <span data-summary-total>IDR 0</span>
                </div>

                <button type="submit" form="booking-form" class="inline-flex w-full items-center justify-center rounded-full bg-rose-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:bg-rose-300" data-confirm-button>
                    Konfirmasi Booking
                </button>
            </aside>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const bookingData = {
            services: @json($services),
            staff: @json($staff),
            customer: @json($customer),
        };

        const MS_PER_MINUTE = 60 * 1000;
        const MS_PER_DAY = 24 * 60 * 60 * 1000;
        const JAKARTA_TIMEZONE = 'Asia/Jakarta';
        const JAKARTA_OFFSET_MINUTES = 7 * 60;

        const padNumber = (value, length = 2) => String(value).padStart(length, '0');
        const padDatePart = (value) => padNumber(value, 2);

        const getJakartaParts = (date) => {
            const formatter = new Intl.DateTimeFormat('en-CA', {
                timeZone: JAKARTA_TIMEZONE,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });

            const parts = formatter.formatToParts(date).reduce((acc, part) => {
                if (part.type !== 'literal') {
                    acc[part.type] = part.value;
                }
                return acc;
            }, {});

            return {
                year: Number(parts.year),
                month: Number(parts.month),
                day: Number(parts.day),
                hour: Number(parts.hour),
                minute: Number(parts.minute),
                second: Number(parts.second),
            };
        };

        const fromJakartaParts = ({ year, month, day, hour = 0, minute = 0, second = 0 }) => {
            const timestamp = Date.UTC(year, month - 1, day, hour, minute, second) - (JAKARTA_OFFSET_MINUTES * MS_PER_MINUTE);
            return new Date(timestamp);
        };

        const getJakartaNow = () => fromJakartaParts(getJakartaParts(new Date()));

        const startOfJakartaDay = (date) => {
            const parts = getJakartaParts(date);
            return fromJakartaParts({
                year: parts.year,
                month: parts.month,
                day: parts.day,
            });
        };

        const startOfJakartaMonth = (date) => {
            const parts = getJakartaParts(date);
            return fromJakartaParts({
                year: parts.year,
                month: parts.month,
                day: 1,
            });
        };

        const addJakartaDays = (date, days) => new Date(date.getTime() + (days * MS_PER_DAY));

        const addJakartaMonths = (date, amount) => {
            const parts = getJakartaParts(date);
            const base = new Date(Date.UTC(parts.year, parts.month - 1, 1));
            base.setUTCMonth(base.getUTCMonth() + amount);

            return fromJakartaParts({
                year: base.getUTCFullYear(),
                month: base.getUTCMonth() + 1,
                day: 1,
            });
        };

        const toLocalISODate = (date) => {
            const parts = getJakartaParts(date);
            return `${parts.year}-${padDatePart(parts.month)}-${padDatePart(parts.day)}`;
        };

        const parseLocalISODate = (isoString) => {
            if (!isoString) {
                return null;
            }

            const [year, month, day] = isoString.split('-').map(Number);

            if ([year, month, day].some((part) => Number.isNaN(part))) {
                return null;
            }

            return fromJakartaParts({ year, month, day });
        };

        const isSameCalendarDay = (dateA, dateB) => {
            if (!dateA || !dateB) {
                return false;
            }

            const partsA = getJakartaParts(dateA);
            const partsB = getJakartaParts(dateB);

            return partsA.year === partsB.year &&
                partsA.month === partsB.month &&
                partsA.day === partsB.day;
        };

        const state = {
            services: bookingData.services.map((service) => ({
                ...service
            })),
            staffAssignments: new Map(),
            selectedDate: null,
            selectedTime: null,
            paymentMethod: null,
            calendarMonth: (() => {
                const today = getJakartaNow();
                const parts = getJakartaParts(today);
                return fromJakartaParts({
                    year: parts.year,
                    month: parts.month,
                    day: 1,
                });
            })(),
            customerPhone: (bookingData.customer.phone || '').trim(),
            isSubmitting: false,
            bookingConfirmed: false,
        };

        const elements = {
            reviewList: document.querySelector('[data-review-list]'),
            staffContainer: document.querySelector('[data-staff-per-service]'),
            calendarTitle: document.querySelector('[data-calendar-title]'),
            calendarGrid: document.querySelector('[data-calendar-grid]'),
            calendarPrev: document.querySelector('[data-calendar-prev]'),
            calendarNext: document.querySelector('[data-calendar-next]'),
            timeOptions: document.querySelector('[data-time-options]'),
            summaryServices: document.querySelector('[data-summary-services]'),
            summaryDuration: document.querySelector('[data-summary-duration]'),
            summaryStaff: document.querySelector('[data-summary-staff]'),
            summaryDatetime: document.querySelector('[data-summary-datetime]'),
            summaryPayment: document.querySelector('[data-summary-payment]'),
            summaryTotal: document.querySelector('[data-summary-total]'),
            paymentInputs: document.querySelectorAll('input[name="payment_method"]'),
            confirmButton: document.querySelector('[data-confirm-button]'),
            confirmationMessage: document.querySelector('[data-confirmation-message]'),
            errorMessage: document.querySelector('[data-error-message]'),
            phoneInput: document.querySelector('input[name="customer_phone"]'),
            form: document.getElementById('booking-form'),
        };

        state.customerPhone = (elements.phoneInput?.value || '').trim();
        state.paymentMethod = Array.from(elements.paymentInputs).find((input) => input.checked)?.value || null;

        const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(value || 0);

        const formatDuration = () => {
            const totalMinutes = state.services.reduce((sum, service) => sum + (Number(service.duration) || 0), 0);
            if (!totalMinutes) {
                return '-';
            }

            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;

            if (hours && minutes) {
                return `${hours} jam ${minutes} menit`;
            }

            if (hours) {
                return `${hours} jam`;
            }

            return `${minutes} menit`;
        };

        const minutesFromTime = (time) => {
            const [hours, minutes] = time.split(':').map(Number);
            return (hours * 60) + minutes;
        };

        const timeFromMinutes = (minutes) => {
            const hrs = Math.floor(minutes / 60).toString().padStart(2, '0');
            const mins = (minutes % 60).toString().padStart(2, '0');
            return `${hrs}:${mins}`;
        };

        const getStaffById = (id) => bookingData.staff.find((staff) => Number(staff.id) === Number(id)) || null;

        const getStaffForService = (serviceId) => bookingData.staff.filter((staff) => staff.service_ids.includes(Number(serviceId)));

        const ensureAssignments = () => {
            const serviceIds = state.services.map((service) => String(service.id));

            // Remove assignments for services that are no longer selected
            Array.from(state.staffAssignments.keys()).forEach((key) => {
                if (!serviceIds.includes(key)) {
                    state.staffAssignments.delete(key);
                }
            });

            // Ensure each service has an available staff member selected
            state.services.forEach((service) => {
                const serviceId = String(service.id);
                const availableStaff = getStaffForService(service.id);
                if (!availableStaff.length) {
                    state.staffAssignments.delete(serviceId);
                    return;
                }

                const currentSelection = state.staffAssignments.get(serviceId);
                const stillValid = availableStaff.some((staff) => Number(staff.id) === Number(currentSelection));

                if (!stillValid) {
                    state.staffAssignments.set(serviceId, Number(availableStaff[0].id));
                }
            });
        };

        const getAssignments = () => {
            ensureAssignments();
            return new Map(state.staffAssignments);
        };

        const goToPrevMonth = () => {
            const today = getJakartaNow();
            const earliestMonth = startOfJakartaMonth(today);
            const prevMonth = addJakartaMonths(state.calendarMonth, -1);
            if (prevMonth < earliestMonth) {
                return;
            }

            state.calendarMonth = prevMonth;
            state.selectedDate = null;
            state.selectedTime = null;
            renderCalendar();
            updateTimeSlotsForSelectedDate();
            updateSummary();
        };

        const goToNextMonth = () => {
            const nextMonth = addJakartaMonths(state.calendarMonth, 1);
            state.calendarMonth = nextMonth;
            state.selectedDate = null;
            state.selectedTime = null;
            renderCalendar();
            updateTimeSlotsForSelectedDate();
            updateSummary();
        };

        const renderReviewServices = () => {
            if (!state.services.length) {
                elements.reviewList.innerHTML = '<p class="rounded-2xl bg-rose-50 px-4 py-3 text-rose-500">Belum ada layanan dipilih. Silakan tambah layanan terlebih dahulu.</p>';
                return;
            }

            elements.reviewList.innerHTML = state.services.map((service) => `
                    <div class="flex flex-col gap-3 rounded-2xl border border-rose-100 bg-rose-50/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">${service.name}</p>
                            <p class="mt-1 text-xs text-gray-500">${service.duration} menit &bull; ${formatCurrency(service.price)}</p>
                        </div>
                        <button type="button" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-500 transition hover:border-rose-400 hover:text-rose-700" data-remove-service="${service.id}">Hapus</button>
                    </div>
                `).join('');

            elements.reviewList.querySelectorAll('[data-remove-service]').forEach((button) => {
                button.addEventListener('click', () => {
                    const serviceId = Number(button.dataset.removeService);
                    state.services = state.services.filter((service) => Number(service.id) !== serviceId);
                    state.staffAssignments.delete(String(serviceId));
                    if (!state.services.length) {
                        state.selectedDate = null;
                        state.selectedTime = null;
                    }
                    updateUI();
                });
            });
        };

            const renderStaffCard = (staff) => {
                if (!staff) {
                    return '';
                }

                const specialisations = staff.specialisations.length ? staff.specialisations.join(' • ') : 'Semua layanan umum';

                return `
                    <div class="mt-4 flex items-start gap-4 rounded-2xl border border-rose-100 bg-white px-4 py-4">
                        <img src="${staff.photo}" alt="${staff.name}" class="h-16 w-16 rounded-full object-cover" loading="lazy">
                        <div class="space-y-1 text-sm">
                            <p class="font-semibold text-gray-900">${staff.name}</p>
                            <p class="text-xs text-gray-500">Spesialisasi: ${specialisations}</p>
                            <p class="text-xs text-gray-500">${staff.bio}</p>
                        </div>
                    </div>
                `;
            };

        const renderStaffSelectors = () => {
            ensureAssignments();

            if (!state.services.length) {
                elements.staffContainer.innerHTML = '<p class="rounded-2xl bg-rose-50 px-4 py-3 text-rose-500">Tambahkan layanan terlebih dahulu untuk melihat stylist yang tersedia.</p>';
                return;
            }

            const markup = state.services.map((service) => {
                const availableStaff = getStaffForService(service.id);
                const selectedId = state.staffAssignments.get(String(service.id));

                if (!availableStaff.length) {
                    return `
                            <div class="rounded-2xl border border-dashed border-rose-200 bg-rose-50/60 p-4">
                                <p class="text-sm font-semibold text-gray-900">${service.name}</p>
                                <p class="mt-1 text-xs text-rose-500">Belum ada stylist yang dapat menangani layanan ini.</p>
                            </div>
                        `;
                }

                const options = availableStaff.map((staff) => `
                        <option value="${staff.id}" ${Number(selectedId) === Number(staff.id) ? 'selected' : ''}>${staff.name}</option>
                    `).join('');

                const staffCard = renderStaffCard(getStaffById(selectedId));

                return `
                        <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
                            <p class="text-sm font-semibold text-gray-900">${service.name}</p>
                            <p class="text-xs text-gray-500">${service.duration} menit &bull; ${formatCurrency(service.price)}</p>
                            <label class="mt-3 block text-xs font-medium text-gray-700">Pilih stylist
                                <select data-service-staff="${service.id}" class="mt-1 w-full rounded-2xl border border-rose-100 bg-white px-4 py-3 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-200">
                                    ${options}
                                </select>
                            </label>
                            ${staffCard}
                        </div>
                    `;
            }).join('');

            elements.staffContainer.innerHTML = markup;

            elements.staffContainer.querySelectorAll('[data-service-staff]').forEach((select) => {
                select.addEventListener('change', () => {
                    const serviceId = String(select.dataset.serviceStaff);
                    state.staffAssignments.set(serviceId, Number(select.value));
                    state.selectedDate = null;
                    state.selectedTime = null;
                    updateUI();
                });
            });
        };

        const getTotalDuration = () => state.services.reduce((sum, service) => sum + (Number(service.duration) || 0), 0);

        const getSchedulesForStaff = (staffId) => {
            const staff = getStaffById(staffId);
            if (!staff) {
                return [];
            }

            if (staff.schedules && staff.schedules.length) {
                return staff.schedules;
            }

            return Array.from({
                length: 7
            }).map((_, index) => ({
                day_of_week: index,
                start_time: '09:00',
                end_time: '18:00',
            }));
        };

        const getBookingsForStaff = (staffId) => {
            const staff = getStaffById(staffId);
            if (!staff) {
                return [];
            }
            return staff.bookings || [];
        };

        const computeSlotsForDate = (date) => {
            const assignments = getAssignments();
            const totalDuration = getTotalDuration();

            if (!assignments.size || !totalDuration) {
                return [];
            }

            const dayParts = getJakartaParts(date);
            const dayOfWeek = new Date(Date.UTC(dayParts.year, dayParts.month - 1, dayParts.day)).getUTCDay();
            const staffIds = Array.from(new Set(Array.from(assignments.values()).filter(Boolean)));

            if (!staffIds.length) {
                return [];
            }

            const dailySchedules = staffIds.map((id) => {
                const schedules = getSchedulesForStaff(id);
                return schedules.find((schedule) => Number(schedule.day_of_week) === Number(dayOfWeek)) || null;
            });

            if (dailySchedules.some((schedule) => !schedule)) {
                return [];
            }

            const maxStart = Math.max(...dailySchedules.map((schedule) => minutesFromTime((schedule.start_time || '09:00').slice(0, 5))));
            const minEnd = Math.min(...dailySchedules.map((schedule) => minutesFromTime((schedule.end_time || '18:00').slice(0, 5))));

            if (Number.isNaN(maxStart) || Number.isNaN(minEnd) || maxStart >= minEnd) {
                return [];
            }

            const slots = [];
            const interval = 30;
            const isoDate = toLocalISODate(date);
            const now = getJakartaNow();
            const nowParts = getJakartaParts(now);
            const isToday = isSameCalendarDay(date, now);

            if (isToday && nowParts.hour >= 15) {
                return [];
            }

            let firstSlotStart = maxStart;

            if (isToday) {
                const nowMinutes = (nowParts.hour * 60) + nowParts.minute;
                const nextSlot = Math.ceil(nowMinutes / interval) * interval;
                firstSlotStart = Math.max(firstSlotStart, nextSlot);
            }

            for (let start = firstSlotStart; start + totalDuration <= minEnd; start += interval) {
                const end = start + totalDuration;

                const conflicts = staffIds.some((staffId) => {
                    const bookings = getBookingsForStaff(staffId).filter((booking) => booking.date === isoDate);
                    return bookings.some((booking) => {
                        const bookingStart = minutesFromTime((booking.start_time || '00:00').slice(0, 5));
                        const bookingEnd = bookingStart + (Number(booking.duration) || 0);
                        return end > bookingStart && start < bookingEnd;
                    });
                });

                if (!conflicts) {
                    slots.push({
                        start,
                        end,
                        label: `${timeFromMinutes(start)} - ${timeFromMinutes(end)}`,
                    });
                }
            }

            return slots;
        };

        const renderTimeSlots = (slots) => {
            if (!slots.length) {
                elements.timeOptions.innerHTML = '<p class="col-span-full rounded-2xl border border-dashed border-rose-200 bg-rose-50 px-4 py-3 text-center text-xs text-rose-500">Tidak ada slot tersedia pada tanggal ini.</p>';
                return;
            }

            elements.timeOptions.innerHTML = slots.map((slot) => {
                const isActive = Number(state.selectedTime) === Number(slot.start);
                return `
                        <button type="button" data-pick-time="${slot.start}" class="rounded-2xl border px-4 py-3 text-sm transition ${isActive ? 'border-rose-500 bg-rose-500 text-white hover:border-rose-500' : 'border-rose-100 bg-white text-gray-700 hover:border-rose-400'}">
                            ${slot.label}
                        </button>
                    `;
            }).join('');

            elements.timeOptions.querySelectorAll('[data-pick-time]').forEach((button) => {
                button.addEventListener('click', () => {
                    state.selectedTime = Number(button.dataset.pickTime);
                    renderTimeSlots(slots);
                    updateSummary();
                });
            });
        };

        const updateTimeSlotsForSelectedDate = () => {
            const assignments = getAssignments();

            if (!assignments.size) {
                elements.timeOptions.innerHTML = '<p class="col-span-full rounded-2xl border border-dashed border-rose-200 bg-rose-50 px-4 py-3 text-center text-xs text-rose-500">Pilih stylist yang tersedia terlebih dahulu.</p>';
                return;
            }

            if (!state.selectedDate) {
                elements.timeOptions.innerHTML = '<p class="col-span-full rounded-2xl border border-dashed border-rose-200 bg-rose-50 px-4 py-3 text-center text-xs text-rose-500">Pilih tanggal terlebih dahulu untuk melihat slot waktu.</p>';
                return;
            }

            const date = parseLocalISODate(state.selectedDate);

            if (!date) {
                state.selectedTime = null;
                elements.timeOptions.innerHTML = '<p class="col-span-full rounded-2xl border border-dashed border-rose-200 bg-rose-50 px-4 py-3 text-center text-xs text-rose-500">Tanggal tidak valid.</p>';
                return;
            }

            const slots = computeSlotsForDate(date);

            if (!slots.some((slot) => Number(slot.start) === Number(state.selectedTime))) {
                state.selectedTime = null;
            }

            renderTimeSlots(slots);
        };

        const renderCalendar = () => {
            const month = startOfJakartaMonth(state.calendarMonth);
            state.calendarMonth = month;
            const today = getJakartaNow();
            const todayStart = startOfJakartaDay(today);
            const monthLabel = month.toLocaleDateString('id-ID', {
                month: 'long',
                year: 'numeric',
                timeZone: JAKARTA_TIMEZONE,
            });

            elements.calendarTitle.textContent = monthLabel;

            const monthParts = getJakartaParts(month);
            const firstDayOfMonth = fromJakartaParts({
                year: monthParts.year,
                month: monthParts.month,
                day: 1,
            });
            const startDay = new Date(Date.UTC(monthParts.year, monthParts.month - 1, 1)).getUTCDay();
            const startDate = addJakartaDays(firstDayOfMonth, -startDay);

            const days = Array.from({ length: 42 }).map((_, index) => {
                const currentDate = addJakartaDays(startDate, index);
                const iso = toLocalISODate(currentDate);
                const currentParts = getJakartaParts(currentDate);
                const inCurrentMonth = currentParts.month === monthParts.month && currentParts.year === monthParts.year;
                const isPast = currentDate < todayStart;
                const slots = inCurrentMonth && !isPast ? computeSlotsForDate(currentDate) : [];
                const available = slots.length > 0;

                return {
                    date: currentDate,
                    iso,
                    label: currentParts.day,
                    inCurrentMonth,
                    available,
                    isPast,
                    slots,
                };
            });

            const selectedDay = days.find((day) => day.iso === state.selectedDate);
            if (!selectedDay || !selectedDay.available) {
                state.selectedDate = null;
                state.selectedTime = null;
            }

            elements.calendarGrid.innerHTML = days.map((day) => {
                const isSelected = state.selectedDate === day.iso;
                const disabled = !day.inCurrentMonth || day.isPast || !day.available;

                const baseClasses = ['relative', 'flex', 'h-12', 'items-center', 'justify-center', 'rounded-2xl', 'border', 'text-sm', 'transition'];

                if (disabled) {
                    baseClasses.push('cursor-not-allowed', 'border-dashed', 'border-rose-100', 'bg-rose-50', 'text-rose-300');
                } else if (isSelected) {
                    baseClasses.push('border-rose-500', 'bg-rose-500', 'font-semibold', 'text-white');
                } else {
                    baseClasses.push('border-rose-100', 'bg-white', 'text-gray-700', 'hover:border-rose-400');
                }

                const indicator = !disabled && !isSelected ? '<span class="absolute bottom-1 h-1.5 w-1.5 rounded-full bg-emerald-400"></span>' : '';

                return `
                        <button type="button" class="${baseClasses.join(' ')}" ${disabled ? 'disabled' : `data-calendar-day="${day.iso}"`}>
                            ${day.label}
                            ${isSelected ? '<span class="absolute bottom-1 h-1.5 w-1.5 rounded-full bg-white"></span>' : indicator}
                        </button>
                    `;
            }).join('');

            elements.calendarGrid.querySelectorAll('[data-calendar-day]').forEach((button) => {
                button.addEventListener('click', () => {
                    const iso = button.dataset.calendarDay;
                    state.selectedDate = iso;
                    const parsedDate = parseLocalISODate(iso);
                    const slots = parsedDate ? computeSlotsForDate(parsedDate) : [];
                    state.selectedTime = null;
                    renderCalendar();
                    renderTimeSlots(slots);
                    updateSummary();
                });
            });

            const prevMonth = addJakartaMonths(month, -1);
            const earliestMonth = startOfJakartaMonth(today);

            elements.calendarPrev.disabled = prevMonth < earliestMonth;

            elements.calendarPrev.classList.toggle('cursor-not-allowed', elements.calendarPrev.disabled);
            elements.calendarPrev.classList.toggle('opacity-50', elements.calendarPrev.disabled);
            elements.calendarPrev.setAttribute('aria-disabled', elements.calendarPrev.disabled ? 'true' : 'false');
        };

        const renderSummary = () => {
            if (!state.services.length) {
                elements.summaryServices.innerHTML = '<p class="rounded-2xl bg-rose-50 px-4 py-3 text-rose-500">Belum ada layanan dipilih.</p>';
            } else {
                elements.summaryServices.innerHTML = state.services.map((service) => {
                    const staffId = state.staffAssignments.get(String(service.id));
                    const staffName = getStaffById(staffId)?.name;
                    return `
                            <div class="rounded-2xl border border-rose-100 bg-rose-50/60 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">${service.name}</p>
                                <p class="text-xs text-gray-500">${service.duration} menit &bull; ${formatCurrency(service.price)}</p>
                                ${staffName ? `<p class="mt-1 text-xs text-rose-500">Stylist: ${staffName}</p>` : ''}
                            </div>
                        `;
                }).join('');
            }

            elements.summaryDuration.textContent = state.services.length ? formatDuration() : '-';

            const assignments = getAssignments();
            if (!assignments.size) {
                elements.summaryStaff.textContent = '-';
            } else {
                const staffNames = Array.from(new Set(Array.from(assignments.values()).map((id) => getStaffById(id)?.name).filter(Boolean)));
                elements.summaryStaff.textContent = staffNames.length ? staffNames.join(', ') : '-';
            }

            if (state.selectedDate && state.selectedTime !== null) {
                const date = parseLocalISODate(state.selectedDate);

                if (date) {
                    const startLabel = timeFromMinutes(state.selectedTime);
                    const endLabel = timeFromMinutes(state.selectedTime + getTotalDuration());
                    elements.summaryDatetime.textContent = `${date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: JAKARTA_TIMEZONE })} • ${startLabel} - ${endLabel}`;
                } else {
                    elements.summaryDatetime.textContent = '-';
                }
            } else {
                elements.summaryDatetime.textContent = '-';
            }

            elements.summaryPayment.textContent = state.paymentMethod === 'pay_at_salon' ? 'Pay at Salon' : '-';

            const totalPrice = state.services.reduce((sum, service) => sum + (Number(service.price) || 0), 0);
            elements.summaryTotal.textContent = formatCurrency(totalPrice);
        };

        const updateSummary = () => {
            renderSummary();
            const assignments = getAssignments();
            const hasAllStaff = state.services.length && Array.from(assignments.keys()).length === state.services.length && Array.from(assignments.values()).every(Boolean);

            if (!elements.confirmButton) {
                return;
            }

            const phoneFilled = Boolean(state.customerPhone && state.customerPhone.trim());
            const readyToSubmit = hasAllStaff &&
                state.selectedDate &&
                state.selectedTime !== null &&
                state.paymentMethod &&
                phoneFilled;

            let shouldDisable = !readyToSubmit;

            if (state.isSubmitting) {
                elements.confirmButton.textContent = 'Memproses...';
                shouldDisable = true;
            } else if (state.bookingConfirmed) {
                elements.confirmButton.textContent = 'Booking Terkonfirmasi';
                shouldDisable = true;
            } else {
                elements.confirmButton.textContent = 'Konfirmasi Booking';
            }

            elements.confirmButton.disabled = shouldDisable;
        };

        const updateUI = () => {
            ensureAssignments();
            renderReviewServices();
            renderStaffSelectors();
            renderCalendar();
            updateTimeSlotsForSelectedDate();
            updateSummary();
        };

        elements.paymentInputs.forEach((input) => {
            input.addEventListener('change', () => {
                state.paymentMethod = input.value;
                updateSummary();
            });
        });

        if (elements.phoneInput) {
            elements.phoneInput.addEventListener('input', () => {
                state.customerPhone = elements.phoneInput.value.trim();
                if (elements.errorMessage && !elements.errorMessage.classList.contains('hidden')) {
                    elements.errorMessage.classList.add('hidden');
                    elements.errorMessage.textContent = '';
                }
                updateSummary();
            });
        }

        elements.calendarPrev.addEventListener('click', goToPrevMonth);
        elements.calendarNext.addEventListener('click', goToNextMonth);

        const buildFormData = () => {
            const formData = new FormData(elements.form);

            formData.delete('services[]');

            state.services.forEach((service) => {
                formData.append('services[]', service.id);
                const staffId = state.staffAssignments.get(String(service.id));
                if (staffId) {
                    formData.append(`assignments[${service.id}]`, staffId);
                }
            });

            if (state.selectedDate) {
                formData.set('booking_date', state.selectedDate);
            }

            if (state.selectedTime !== null) {
                formData.set('booking_time', timeFromMinutes(state.selectedTime));
            }

            if (state.paymentMethod) {
                formData.set('payment_method', state.paymentMethod);
            }

            return formData;
        };

        elements.form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (state.isSubmitting || state.bookingConfirmed || elements.confirmButton.disabled) {
                return;
            }

            if (elements.errorMessage) {
                elements.errorMessage.classList.add('hidden');
                elements.errorMessage.textContent = '';
            }

            state.isSubmitting = true;
            updateSummary();

            const csrfToken = elements.form.querySelector('input[name="_token"]')?.value || '';

            try {
                const response = await fetch(elements.form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: buildFormData(),
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => null);
                    let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';

                    if (data?.errors) {
                        const firstError = Object.values(data.errors).flat().find(Boolean);
                        if (firstError) {
                            errorMessage = firstError;
                        }
                    } else if (data?.message) {
                        errorMessage = data.message;
                    }

                    if (elements.errorMessage) {
                        elements.errorMessage.textContent = errorMessage;
                        elements.errorMessage.classList.remove('hidden');
                    }

                    return;
                }

                await response.json().catch(() => ({}));

                state.bookingConfirmed = true;

                if (elements.confirmationMessage) {
                    elements.confirmationMessage.classList.remove('hidden');
                }
            } catch (error) {
                if (elements.errorMessage) {
                    elements.errorMessage.textContent = 'Tidak dapat terhubung ke server. Silakan coba lagi.';
                    elements.errorMessage.classList.remove('hidden');
                }
            } finally {
                state.isSubmitting = false;
                updateSummary();
            }
        });

        updateUI();
    });
</script>
@endpush
