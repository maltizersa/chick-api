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
        "CREATE TABLE hotel_rooms (
            room_id INT AUTO_INCREMENT PRIMARY KEY,
            hotel_id INT NOT NULL,
            room_name VARCHAR(255) NOT NULL,
            price INT NOT NULL,
            image_path VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(hotel_id) REFERENCES hotelsdb(id) ON DELETE CASCADE
        )"
       );

    //    DB::insert(
    //     "INSERT INTO hotel_rooms (hotel_id, room_name, price) VALUES
    //     (1, 'Delux', 562)"
    //    );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
