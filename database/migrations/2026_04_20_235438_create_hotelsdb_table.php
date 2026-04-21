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
        // Schema::create('hotelsdb', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        DB::statement(
            "CREATE TABLE hotelsdb (
                id INT AUTO_INCREMENT PRIMARY KEY,
                hotel_name VARCHAR(255) NOT NULL,
                hotel_address VARCHAR(255) NOT NULL,
                hotel_contact VARCHAR(11) NOT NULL,
                hotel_image_loc VARCHAR(255) NOT NULL,
                hotel_longitude DOUBLE NOT NULL,
                hotel_latitude DOUBLE NOT NULL,
                status ENUM('hotel', 'inn') NOT NULL DEFAULT 'hotel',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            "
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotelsdb');
    }
};
