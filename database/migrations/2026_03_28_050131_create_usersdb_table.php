<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('usersdb', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        DB::statement(
            "CREATE TABLE usersdb (
                uid INT AUTO_INCREMENT PRIMARY KEY,
                -- username VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                first_name VARCHAR(24) NULL,
                middle_name VARCHAR(64) NULL,
                last_name VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                phone_number VARCHAR(11) NOT NULL)
            "
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usersdb');
    }
};
