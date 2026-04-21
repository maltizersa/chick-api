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
        DB::statement(
            "CREATE TABLE hotel_amenities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                hotel_id INT NOT NULL,
                amenity_id INT NOT NULL,
                FOREIGN KEY (hotel_id) REFERENCES hotelsdb(id) ON DELETE CASCADE,
                FOREIGN KEY (amenity_id) REFERENCES amenities(id)
            )"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_amenities');
    }
};
