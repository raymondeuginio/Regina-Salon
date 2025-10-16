{{-- filepath: c:\akuliah\Regina-Salon\reginasalon\resources\views\errors\minimal.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Regina Salon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <section class="relative flex min-h-screen items-center bg-gradient-to-br from-rose-50 via-white to-rose-100">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none absolute -left-24 top-10 h-72 w-72 rounded-full bg-rose-200/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-16 bottom-16 h-80 w-80 rounded-full bg-rose-300/30 blur-3xl"></div>
        </div>

        <div class="relative mx-auto flex w-full max-w-5xl flex-col items-center gap-14 px-4 text-center sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-8">
                <span class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white/70 px-4 py-1 text-xs font-semibold uppercase tracking-[0.4em] text-rose-500">
                    @yield('badge', 'Error')
                </span>

                <div class="flex flex-col items-center gap-6">
                    <span class="relative inline-flex">
                        <span class="absolute inset-0 rounded-[3rem] bg-rose-500/10 blur-2xl"></span>
                        <span class="relative rounded-[3rem] border border-rose-100 bg-white px-10 py-6 text-6xl font-bold text-rose-500 shadow-lg shadow-rose-200/60 sm:text-7xl">
                            @yield('code')
                        </span>
                    </span>

                    <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
                        @yield('heading')
                    </h1>
                    <p class="max-w-2xl text-base leading-relaxed text-gray-600">
                        @yield('message')
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-200 transition hover:bg-rose-700">
                    Kembali ke Beranda
                </a>
                <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-8 py-3 text-sm font-semibold text-rose-600 transition hover:border-rose-400 hover:text-rose-700">
                    Lihat Layanan Kami
                </a>
            </div>
        </div>
    </section>
</body>

</html>