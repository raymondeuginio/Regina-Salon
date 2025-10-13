@extends('layouts.public')

@section('title', 'Pilih Layanan Regina Salon')

@section('content')
    <section class="bg-white py-16">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-400">Branch</p>
                    <div class="mt-2 flex flex-col gap-2 text-gray-900">
                        <h1 class="text-3xl font-bold sm:text-4xl">{{ $store['name'] }}</h1>
                        <p class="text-sm text-gray-500">{{ $store['address'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm text-rose-600">
                    <span class="rounded-full bg-rose-50 px-4 py-2 font-medium text-rose-600">Store ID: {{ $storeId }}</span>
                    <a href="#" class="font-semibold transition hover:text-rose-700">Change Branch</a>
                </div>
            </div>

            <div class="mt-12">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-400">Select a Service</p>
                        <h2 class="mt-2 text-2xl font-bold text-gray-900">Temukan perawatan terbaik untuk Anda</h2>
                    </div>
                    <div class="flex items-center gap-3 rounded-full border border-rose-100 bg-rose-50 px-5 py-3 text-xs font-medium text-rose-500">
                        <span class="inline-flex h-2 w-2 rounded-full bg-rose-500"></span>
                        Multi-selection enabled &mdash; pilih beberapa layanan sekaligus
                    </div>
                </div>

                @if ($categories->isEmpty())
                    <div class="mt-10 rounded-3xl border border-dashed border-rose-200 bg-rose-50/70 p-10 text-center text-sm text-gray-500">
                        Layanan belum tersedia untuk store ini. Silakan kembali lagi nanti.
                    </div>
                @else
                    @php
                        $defaultCategoryId = (int) ($defaultCategoryId ?: optional($categories->first())->id);
                    @endphp

                    <div class="mt-10">
                        <div class="flex flex-wrap gap-3" role="tablist" data-category-buttons>
                            @foreach ($categories as $category)
                                <button
                                    type="button"
                                    data-target="category-{{ $category->id }}"
                                    class="rounded-full border border-rose-100 bg-white px-5 py-2 text-sm font-semibold text-gray-600 shadow-sm transition hover:border-rose-200 hover:text-rose-600 focus:outline-none data-[active=true]:border-rose-500 data-[active=true]:bg-rose-500 data-[active=true]:text-white"
                                    data-active="{{ $category->id === $defaultCategoryId ? 'true' : 'false' }}"
                                >
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
                            <form id="service-selection" class="space-y-8" method="GET" action="{{ url('/booking') }}">
                                <input type="hidden" name="store_id" value="{{ $storeId }}">

                                @foreach ($categories as $category)
                                    <div
                                        id="category-{{ $category->id }}"
                                        data-category-panel
                                        @if($category->id !== $defaultCategoryId) hidden @endif
                                        class="space-y-5"
                                    >
                                        <header class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">{{ $category->name }}</p>
                                                <h3 class="mt-2 text-xl font-semibold text-gray-900">{{ $category->services->count() }} layanan siap dipilih</h3>
                                            </div>
                                            <span class="rounded-full bg-rose-50 px-4 py-2 text-xs font-medium text-rose-600">Kategori</span>
                                        </header>

                                        <ul class="space-y-4">
                                            @foreach ($category->services as $service)
                                                <li>
                                                    <div
                                                        class="flex w-full items-center gap-6 rounded-3xl border border-rose-100 bg-white px-6 py-5 shadow-sm transition hover:border-rose-300"
                                                        data-service-card
                                                        data-service-id="{{ $service->id }}"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            name="services[]"
                                                            value="{{ $service->id }}"
                                                            data-service-id="{{ $service->id }}"
                                                            data-price="{{ $service->price }}"
                                                            data-duration="{{ $service->duration }}"
                                                            class="hidden"
                                                        >
                                                        <span class="flex-1">
                                                            <span class="flex flex-wrap items-start justify-between gap-4">
                                                                <span>
                                                                    <span class="block text-lg font-semibold text-gray-900">{{ $service->name }}</span>
                                                                    <span class="mt-2 block text-sm text-gray-500">{{ $service->description }}</span>
                                                                </span>
                                                                <span class="text-right">
                                                                    <span class="block text-lg font-semibold text-gray-900">IDR {{ number_format($service->price, 0, ',', '.') }}</span>
                                                                    <span class="mt-2 inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-600">{{ $service->duration }} menit</span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                        <div class="flex flex-col items-center gap-2 sm:flex-row sm:gap-3">
                                                            <button
                                                                type="button"
                                                                class="flex h-11 w-11 flex-none items-center justify-center rounded-full border-2 border-rose-200 text-base font-semibold text-rose-500 transition focus:outline-none hover:border-rose-300 hover:text-rose-600"
                                                                data-add-service="{{ $service->id }}"
                                                                aria-label="Tambah layanan {{ $service->name }}"
                                                            >
                                                                <span aria-hidden="true">+</span>
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="hidden flex h-11 w-11 flex-none items-center justify-center rounded-full border-2 border-rose-200 text-sm text-rose-500 transition focus:outline-none hover:border-rose-500 hover:bg-white"
                                                                data-remove-service-card="{{ $service->id }}"
                                                                aria-label="Hapus layanan {{ $service->name }}"
                                                                title="Hapus dari pilihan"
                                                            >
                                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L19.16 19.673a2.25 2.25 0 01-2.244 2.077H7.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </form>

                            <aside class="space-y-6 rounded-3xl border border-rose-100 bg-white p-8 shadow-sm">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-400">Ringkasan Booking</p>
                                    <h4 class="mt-2 text-xl font-semibold text-gray-900">Layanan yang Anda pilih</h4>
                                </div>
                                <div class="space-y-3 text-sm" data-selected-services>
                                    <p class="rounded-2xl bg-rose-50 px-4 py-3 text-rose-500">Belum ada layanan dipilih.</p>
                                </div>
                                <dl class="space-y-3 text-sm">
                                    <div class="flex items-center justify-between text-gray-500">
                                        <dt>Total layanan</dt>
                                        <dd class="font-semibold text-gray-900" data-selected-count>0</dd>
                                    </div>
                                    <div class="flex items-center justify-between text-gray-500">
                                        <dt>Estimasi durasi</dt>
                                        <dd class="font-semibold text-gray-900" data-total-duration>-</dd>
                                    </div>
                                    <div class="flex items-center justify-between text-gray-500">
                                        <dt>Estimasi biaya</dt>
                                        <dd class="font-semibold text-gray-900" data-total-price>-</dd>
                                    </div>
                                </dl>
                                <button
                                    form="service-selection"
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-rose-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:bg-rose-300"
                                    data-submit-button
                                >
                                    Lanjut ke Booking
                                </button>
                                <p class="text-xs leading-relaxed text-gray-500">
                                    Setelah memilih layanan, Anda dapat menentukan tanggal, stylist, dan catatan khusus pada langkah berikutnya.
                                </p>
                            </aside>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@if ($categories->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const categoryButtons = document.querySelectorAll('[data-category-buttons] [data-target]');
                const panels = document.querySelectorAll('[data-category-panel]');

                categoryButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        categoryButtons.forEach((btn) => btn.dataset.active = 'false');
                        panels.forEach((panel) => {
                            panel.hidden = panel.id !== button.dataset.target;
                        });

                        button.dataset.active = 'true';
                    });
                });

                const selectionForm = document.getElementById('service-selection');
                if (!selectionForm) {
                    return;
                }

                const serviceData = new Map(@json($servicePayload).map(service => [String(service.id), service]));
                const checkboxes = selectionForm.querySelectorAll('input[name="services[]"]');
                const countTarget = document.querySelector('[data-selected-count]');
                const durationTarget = document.querySelector('[data-total-duration]');
                const priceTarget = document.querySelector('[data-total-price]');
                const submitButton = document.querySelector('[data-submit-button]');
                const selectedList = document.querySelector('[data-selected-services]');

                const formatPrice = (value) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(value || 0);

                const renderSelectedServices = (selected) => {
                    if (!selected.length) {
                        selectedList.innerHTML = '<p class="rounded-2xl bg-rose-50 px-4 py-3 text-rose-500">Belum ada layanan dipilih.</p>';
                        return;
                    }

                    selectedList.innerHTML = selected.map((checkbox) => {
                        const service = serviceData.get(String(checkbox.dataset.serviceId));
                        if (!service) {
                            return '';
                        }

                        return `
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-rose-100 bg-rose-50/70 px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">${service.name}</p>
                                    <p class="mt-1 text-xs text-gray-500">${service.duration} menit &bull; ${formatPrice(service.price)}</p>
                                </div>
                                <button
                                    type="button"
                                    class="flex h-8 w-8 flex-none items-center justify-center rounded-full border border-rose-200 text-xs font-semibold text-rose-500 transition hover:border-rose-500 hover:bg-white"
                                    data-remove-summary="${service.id}"
                                    aria-label="Hapus layanan ${service.name} dari pilihan"
                                >
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L19.16 19.673a2.25 2.25 0 01-2.244 2.077H7.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        `;
                    }).join('');

                    selectedList.querySelectorAll('[data-remove-summary]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const checkbox = selectionForm.querySelector(`input[name="services[]"][data-service-id="${button.dataset.removeSummary}"]`);
                            if (!checkbox) {
                                return;
                            }

                            checkbox.checked = false;
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    });
                };

                const setCardState = (checkbox) => {
                    const card = selectionForm.querySelector(`[data-service-card][data-service-id="${checkbox.dataset.serviceId}"]`);
                    if (!card) {
                        return;
                    }

                    const service = serviceData.get(String(checkbox.dataset.serviceId));
                    const addButton = card.querySelector('[data-add-service]');
                    const removeButton = card.querySelector('[data-remove-service-card]');

                    if (checkbox.checked) {
                        card.classList.add('border-rose-500', 'bg-rose-50');
                        card.classList.remove('border-rose-100', 'bg-white');

                        if (addButton) {
                            addButton.disabled = true;
                            addButton.classList.remove('border-rose-200', 'text-rose-500', 'hover:border-rose-300', 'hover:text-rose-600');
                            addButton.classList.add('border-rose-600', 'bg-rose-600', 'text-white', 'cursor-not-allowed');
                            if (service) {
                                addButton.setAttribute('aria-label', `${service.name} sudah ditambahkan`);
                            }
                        }

                        if (removeButton) {
                            removeButton.classList.remove('hidden');
                            removeButton.classList.add('border-rose-600', 'bg-rose-600', 'text-white');
                            removeButton.classList.remove('border-rose-200', 'text-rose-500');
                            if (service) {
                                removeButton.setAttribute('aria-label', `Hapus layanan ${service.name}`);
                            }
                        }
                    } else {
                        card.classList.add('border-rose-100', 'bg-white');
                        card.classList.remove('border-rose-500', 'bg-rose-50');

                        if (addButton) {
                            addButton.disabled = false;
                            addButton.classList.remove('border-rose-600', 'bg-rose-600', 'text-white', 'cursor-not-allowed');
                            addButton.classList.add('border-rose-200', 'text-rose-500');
                            if (!addButton.classList.contains('hover:border-rose-300')) {
                                addButton.classList.add('hover:border-rose-300', 'hover:text-rose-600');
                            }
                            if (service) {
                                addButton.setAttribute('aria-label', `Tambah layanan ${service.name}`);
                            }
                        }

                        if (removeButton) {
                            removeButton.classList.add('hidden');
                            removeButton.classList.add('border-rose-200', 'text-rose-500');
                            removeButton.classList.remove('border-rose-600', 'bg-rose-600', 'text-white');
                        }
                    }
                };

                const updateSummary = () => {
                    const selected = Array.from(checkboxes).filter((checkbox) => checkbox.checked);

                    countTarget.textContent = selected.length;

                    if (!selected.length) {
                        durationTarget.textContent = '-';
                        priceTarget.textContent = '-';
                        submitButton.disabled = true;
                        renderSelectedServices(selected);
                        return;
                    }

                    const totals = selected.reduce(
                        (carry, checkbox) => {
                            const service = serviceData.get(String(checkbox.dataset.serviceId));
                            if (!service) {
                                return carry;
                            }

                            return {
                                price: carry.price + Number(service.price || 0),
                                duration: carry.duration + Number(service.duration || 0),
                            };
                        },
                        { price: 0, duration: 0 },
                    );

                    durationTarget.textContent = `${totals.duration} menit`;
                    priceTarget.textContent = formatPrice(totals.price);
                    submitButton.disabled = false;
                    renderSelectedServices(selected);
                };

                checkboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', () => {
                        setCardState(checkbox);
                        updateSummary();
                    });
                });

                const addButtons = selectionForm.querySelectorAll('[data-add-service]');
                addButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const checkbox = selectionForm.querySelector(`input[name="services[]"][data-service-id="${button.dataset.addService}"]`);
                        if (!checkbox) {
                            return;
                        }

                        if (!checkbox.checked) {
                            checkbox.checked = true;
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                });

                const removeButtons = selectionForm.querySelectorAll('[data-remove-service-card]');
                removeButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const checkbox = selectionForm.querySelector(`input[name="services[]"][data-service-id="${button.dataset.removeServiceCard}"]`);
                        if (!checkbox) {
                            return;
                        }

                        if (checkbox.checked) {
                            checkbox.checked = false;
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                });

                Array.from(checkboxes).forEach(setCardState);
                updateSummary();
            });
        </script>
    @endpush
@endif