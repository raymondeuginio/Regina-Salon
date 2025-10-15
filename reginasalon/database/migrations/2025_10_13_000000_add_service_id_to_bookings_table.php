<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'service_id')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->foreignId('service_id')
                    ->nullable()
                    ->after('store_id')
                    ->constrained()
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('bookings', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }
        });
    }
};
