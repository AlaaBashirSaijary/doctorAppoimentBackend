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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الغرفة
            $table->enum('type', ['chat', 'video'])->default('chat'); // نوع الغرفة (دردشة أو مكالمة فيديو)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // المستخدم الذي أنشأ الغرفة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
