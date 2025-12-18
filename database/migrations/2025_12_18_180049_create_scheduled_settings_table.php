<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');  // sync_time (1–7)
            $table->string('db_location')->nullable();
            $table->dateTime('last_sync')->nullable();  // ✅ FIXED
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_settings');
    }
};
