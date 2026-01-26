<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_push_queue', function (Blueprint $table) {
            $table->id();
            $table->json('student_data');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->integer('retry_count')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_push_queue');
    }
};
