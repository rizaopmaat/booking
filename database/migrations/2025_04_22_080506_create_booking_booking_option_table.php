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
        Schema::create('booking_booking_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_option_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1); // For per_person options, this could be the number of guests
            $table->decimal('price_at_booking', 8, 2); // Store the price as it was when booked
            $table->decimal('price_at_booking_total', 8, 2); // Store the total price at booking time
            $table->timestamps();

            // Optional: Ensure a booking can only have a specific option once
            // $table->unique(['booking_id', 'booking_option_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_booking_option');
    }
};
