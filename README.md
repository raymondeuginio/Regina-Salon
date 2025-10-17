# Regina Salon & Spa

## Overview
Regina Salon & Spa is a Laravel 12 application that digitises the end-to-end booking experience for a premium beauty salon. The site showcases curated services, highlights the salon brand, and lets registered customers assemble multi-service appointments, assign their preferred stylists, and confirm their visit online.

## Features
- **Marketing landing page** with brand story, services highlights, and contact details tailored to Regina Salon.
- **Service catalogue** grouped by category, complete with pricing, durations, and a smart summary panel that tracks the customer’s selections in real time.
- **Guided booking flow** that validates stylist availability, prevents schedule conflicts, and notifies the team when a booking is confirmed.
- **Authentication & profile management** powered by Laravel Breeze so returning customers can reuse their stored contact details.
- **Seeded demo data** for stores, categories, services, staff, and admin accounts to help you explore the experience instantly.

## Tech Stack
- **Backend:** PHP 8.2, Laravel 12, Filament admin tooling
- **Frontend:** Blade templates, Tailwind CSS, Alpine.js, Vite asset bundler
- **Database:** SQLite by default (configurable to MySQL/PostgreSQL)
- **Tooling:** PHPUnit for tests, Laravel Sail for containers, Laravel Pint for code style

## Installation
1. **Clone & install dependencies**
   ```bash
   git clone <repository-url>
   cd Regina-Salon/reginasalon
   composer install
   npm install
   ```
2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   By default the project uses SQLite. Ensure the database file exists:
   ```bash
   touch database/database.sqlite
   ```
   Update `.env` if you prefer a different database engine.
3. **Run migrations & seed demo content**
   ```bash
   php artisan migrate --seed
   ```

## Usage
- **Development servers**
  ```bash
  php artisan serve
  npm run dev
  ```
  Alternatively, use the combined watcher:
  ```bash
  composer run dev
  ```
- **Access the app** at `http://localhost:8000` and log in with one of the seeded accounts, e.g. `regina@salon.com` / `regina123`.

## Screenshot
Add project screenshots to `docs/` and reference them here. For example:
