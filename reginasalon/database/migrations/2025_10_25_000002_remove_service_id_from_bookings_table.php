<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('bookings', 'service_id')) {
            Schema::table('bookings', static function (Blueprint $table): void {
                $table->dropConstrainedForeignId('service_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('bookings', 'service_id')) {
            Schema::table('bookings', static function (Blueprint $table): void {
                $table->foreignId('service_id')
                    ->nullable()
                    ->constrained('services')
                    ->nullOnDelete();
            });
        }
    }
};
