@extends('layouts.public')

@section('title', 'Regina Salon & Spa')

@section('content')
    <section id="home" class="bg-gradient-to-br from-rose-50 via-white to-rose-100">
        <div class="mx-auto flex w-full max-w-6xl flex-col-reverse items-center gap-10 px-4 py-20 sm:px-6 lg:flex-row lg:gap-16 lg:px-8">
            <div class="w-full lg:w-1/2">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-rose-500">Beauty &amp; Wellness</p>
                <h1 class="mt-4 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                    Shine Brighter with Personalized Salon Experiences
                </h1>
                <p class="mt-6 text-base leading-relaxed text-gray-600">
                    Regina Salon menghadirkan rangkaian perawatan rambut, wajah, dan tubuh yang dikurasi khusus untuk Anda.
                    Nikmati pelayanan profesional dari stylist berpengalaman dengan suasana nyaman dan hangat.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-200 transition hover:bg-rose-700">Mulai Pilih Layanan</a>
                    <a href="#about" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-6 py-3 text-sm font-semibold text-rose-600 transition hover:border-rose-400 hover:text-rose-700">Pelajari Lebih Lanjut</a>
                </div>
            </div>
            <div class="w-full lg:w-1/2">
                <div class="relative">
                    <div class="absolute -inset-8 rounded-[2.5rem] bg-rose-200/40 blur-3xl"></div>
                    <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=900&q=80" alt="Salon experience" class="relative w-full rounded-[2.5rem] object-cover shadow-xl shadow-rose-200" />
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="bg-white py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-rose-500">About Us</p>
                    <h2 class="mt-4 text-3xl font-bold text-gray-900">Elegan, nyaman, dan penuh perhatian.</h2>
                    <p class="mt-6 text-base leading-relaxed text-gray-600">
                        Sejak 2001, Regina Salon membantu ribuan pelanggan tampil percaya diri melalui layanan profesional.
                        Tim kami menggabungkan teknik modern dan produk terbaik untuk memberikan hasil yang memuaskan.
                    </p>
                    <div class="mt-8 grid grid-cols-2 gap-6 text-sm text-gray-700">
                        <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-6">
                            <p class="text-3xl font-bold text-rose-600">20+</p>
                            <p class="mt-2 font-medium">Tahun pengalaman</p>
                        </div>
                        <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-6">
                            <p class="text-3xl font-bold text-rose-600">35K+</p>
                            <p class="mt-2 font-medium">Pelanggan puas</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl border border-rose-100 bg-rose-50/70 p-10">
                    <h3 class="text-lg font-semibold text-gray-900">Signature Experiences</h3>
                    <ul class="mt-6 space-y-4 text-sm text-gray-700">
                        <li class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-rose-100 text-xs font-semibold text-rose-600">1</span>
                            <span>Stylist tersertifikasi dengan konsultasi personal untuk memahami kebutuhan Anda.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-rose-100 text-xs font-semibold text-rose-600">2</span>
                            <span>Produk premium pilihan untuk hasil yang tahan lama dan aman.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-rose-100 text-xs font-semibold text-rose-600">3</span>
                            <span>Ruang layanan eksklusif dengan standar kebersihan tinggi dan suasana relaksasi.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-rose-600 py-16 text-white">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between">
                <div class="lg:max-w-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-rose-100">Why Choose Us</p>
                    <h2 class="mt-4 text-3xl font-bold">Layanan yang memanjakan dari ujung rambut hingga kaki.</h2>
                    <p class="mt-4 text-sm leading-relaxed text-rose-100">
                        Kami percaya setiap kunjungan adalah momen spesial. Dari greeting pertama, aromaterapi ruangan, hingga sentuhan akhir stylist, semua kami siapkan agar Anda merasa dihargai.
                    </p>
                </div>
                <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl bg-white/10 p-6 backdrop-blur">
                        <p class="text-xl font-semibold">Penjadwalan fleksibel</p>
                        <p class="mt-2 text-sm text-rose-100">Pilih jadwal terbaik Anda secara online, kapan pun.</p>
                    </div>
                    <div class="rounded-3xl bg-white/10 p-6 backdrop-blur">
                        <p class="text-xl font-semibold">Tim profesional</p>
                        <p class="mt-2 text-sm text-rose-100">Therapist tersertifikasi siap membantu setiap detail.</p>
                    </div>
                    <div class="rounded-3xl bg-white/10 p-6 backdrop-blur">
                        <p class="text-xl font-semibold">Produk eksklusif</p>
                        <p class="mt-2 text-sm text-rose-100">Kolaborasi dengan brand internasional pilihan.</p>
                    </div>
                    <div class="rounded-3xl bg-white/10 p-6 backdrop-blur">
                        <p class="text-xl font-semibold">Suasana nyaman</p>
                        <p class="mt-2 text-sm text-rose-100">Interior modern dan privat untuk relaksasi maksimal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="bg-white py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-rose-500">Contact Us</p>
                    <h2 class="mt-4 text-3xl font-bold text-gray-900">Kami siap membantu reservasi Anda.</h2>
                    <p class="mt-4 text-base text-gray-600">Hubungi kami untuk konsultasi layanan, ketersediaan jadwal, atau pertanyaan lainnya.</p>
                    <div class="mt-8 space-y-4 text-sm text-gray-700">
                        <p><span class="font-semibold text-rose-600">Phone:</span> +62 123-4567-890</p>
                        <p><span class="font-semibold text-rose-600">Email:</span> hello@reginasalon.com</p>
                        <p><span class="font-semibold text-rose-600">Address:</span> Jl. Sunset Avenue No. 10, Jakarta</p>
                    </div>
                </div>
                <div class="rounded-3xl border border-rose-100 bg-rose-50/70 p-10">
                    <h3 class="text-lg font-semibold text-gray-900">Jam Operasional</h3>
                    <ul class="mt-6 space-y-3 text-sm text-gray-700">
                        <li class="flex items-center justify-between">
                            <span>Senin - Jumat</span>
                            <span>10:00 - 20:00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Sabtu</span>
                            <span>09:00 - 21:00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Minggu &amp; Libur Nasional</span>
                            <span>09:00 - 18:00</span>
                        </li>
                    </ul>
                    <a href="{{ route('services.index') }}" class="mt-10 inline-flex w-full items-center justify-center rounded-full bg-rose-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">Buat Janji Sekarang</a>
                </div>
            </div>
        </div>
    </section>
@endsection
