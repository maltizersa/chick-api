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
        DB::statement("CREATE TABLE reviews (
            review_id INT AUTO_INCREMENT PRIMARY KEY,
            uid INT NOT NULL,
            hotel_id INT NOT NULL,
            rating INT NOT NULL,
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uid) REFERENCES usersdb(uid) ON DELETE RESTRICT,
            FOREIGN KEY (hotel_id) REFERENCES hotelsdb(id) ON DELETE RESTRICT)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
