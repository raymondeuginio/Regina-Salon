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
        Schema::create('booking_item_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->constrained('booking_items')->onDelete('cascade');
            $table->foreignId('addon_id')->constrained('service_addons')->onDelete('cascade');
            $table->decimal('final_price', 12, 2);
            $table->integer('final_duration');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_item_addons');
    }
};
