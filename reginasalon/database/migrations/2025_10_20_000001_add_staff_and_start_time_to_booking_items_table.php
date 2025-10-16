<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('booking_items', 'staff_id')) {
                $table->foreignId('staff_id')
                    ->after('service_id')
                    ->constrained('staff')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('booking_items', 'start_time')) {
                $table->time('start_time')->after('staff_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table): void {
            if (Schema::hasColumn('booking_items', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('booking_items', 'staff_id')) {
                $table->dropConstrainedForeignId('staff_id');
            }
        });
    }
};
