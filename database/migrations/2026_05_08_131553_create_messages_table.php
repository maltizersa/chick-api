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
            "
                CREATE TABLE messages (
                    message_id INT AUTO_INCREMENT PRIMARY KEY,
                    sender_id INT NOT NULL,
                    receiver_id INT NOT NULL, 
                    message TEXT NOT NULL,
                    is_read TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                    INDEX(sender_id),
                    INDEX(receiver_id),

                    FOREIGN KEY (sender_id) REFERENCES usersdb(uid) ON DELETE CASCADE,
                    FOREIGN KEY (receiver_id) REFERENCES usersdb(uid) ON DELETE CASCADE
                )
            "
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
