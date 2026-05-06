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
        // Schema::create('bookings', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        DB::statement("CREATE TABLE bookings (
            id BIGINT PRIMARY KEY,
            uid INT NOT NULL,
            hotel_id INT NOT NULL,
            room_type VARCHAR(255) NOT NULL,
            check_in DATE NOT NULL,
            check_out DATE NOT NULL,
            FOREIGN KEY (uid) REFERENCES usersdb(uid),
            FOREIGN KEY (hotel_id) REFERENCES  hotelsdb(id))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
