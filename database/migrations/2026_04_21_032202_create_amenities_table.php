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
        // Schema::create('amenities', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        DB::statement(
            "CREATE TABLE amenities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name ENUM('Wifi', 'Parking', 'Pool', 'Gym', 'Breakfast') NOT NULL
            )"
        );

        DB::statement(
            "INSERT INTO amenities (name) VALUES 
            ('Wifi'), 
            ('Parking'), 
            ('Pool'), 
            ('Gym'), 
            ('Breakfast')"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
