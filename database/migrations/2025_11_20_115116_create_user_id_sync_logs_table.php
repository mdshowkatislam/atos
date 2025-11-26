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
       Schema::create('user_id_sync_logs', function (Blueprint $table) {
        $table->id();

        // Job info
        $table->integer('chunk_size');  // how many profileIds were sent

        // Response info
        $table->boolean('success')->default(false);
        $table->integer('inserted_count')->default(0);
        $table->integer('errors_count')->default(0);

        // Detailed lists (JSON)
        $table->json('duplicate_existing_before')->nullable();
        $table->json('duplicate_from_race_condition')->nullable();

        // Request & Response debugging
        $table->json('sent_payload')->nullable();
        $table->json('api_raw_response')->nullable();
        $table->integer('response_status')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_id_sync_logs');
    }
};
