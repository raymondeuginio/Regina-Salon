<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'Regina Salon'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-rose-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            <nav class="bg-white shadow-sm">
                <div class="mx-auto flex h-20 w-full max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold text-rose-600">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 font-bold text-rose-600">R</span>
                        <span>Regina Salon</span>
                    </a>
                    <div class="hidden items-center gap-6 text-sm font-medium text-gray-600 md:flex">
                        @php
                            $navLinks = [
                                ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home')],
                                ['label' => 'Service', 'href' => route('services.index'), 'active' => request()->routeIs('services.index')],
                                ['label' => 'About Us', 'href' => route('home') . '#about', 'active' => false],
                                ['label' => 'Contact Us', 'href' => route('home') . '#contact', 'active' => false],
                            ];
                        @endphp

                        @foreach ($navLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                class="transition hover:text-rose-600 {{ $link['active'] ? 'text-rose-600' : '' }}"
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-rose-600 transition hover:text-rose-700 md:inline">Sign In</a>
                        <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">
                            Book Now
                        </a>
                    </div>
                </div>
            </nav>

            <main class="flex-1">
                @yield('content')
            </main>

            <footer class="bg-white py-8 text-sm text-gray-500">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                    <p>&copy; {{ now()->format('Y') }} Regina Salon. All rights reserved.</p>
                    <div class="flex flex-wrap items-center gap-6">
                        <a href="tel:+621234567890" class="transition hover:text-rose-600">+62 123-4567-890</a>
                        <a href="mailto:hello@reginasalon.com" class="transition hover:text-rose-600">hello@reginasalon.com</a>
                        <span>Jl. Sunset Avenue No. 10, Jakarta</span>
                    </div>
                </div>
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
