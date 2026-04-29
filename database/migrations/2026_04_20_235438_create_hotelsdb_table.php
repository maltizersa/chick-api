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

        DB::insert(
            "
                INSERT INTO hotelsdb (
                    hotel_name,
                    hotel_address,
                    hotel_contact,
                    hotel_image_loc,
                    hotel_longitude,
                    hotel_latitude,
                    status
                ) VALUES (
                    'Lipit Sur Inn',
                    'Lipit Sur, Mangaldan, Pangasinan',
                    '09123456789',
                    'storage/lisland.jpg',  
                    120.515493,
                    16.059599,
                    'inn'
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
