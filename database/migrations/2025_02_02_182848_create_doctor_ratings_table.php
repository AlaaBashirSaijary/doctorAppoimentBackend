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
        Schema::create('doctor_ratings', function (Blueprint $table) {
             $table->id();
    $table->unsignedBigInteger('doctor_id');
    $table->unsignedBigInteger('patient_id');
    $table->unsignedTinyInteger('rating')->comment('1 to 5 stars');
    $table->text('review')->nullable();
    $table->timestamps();

    $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_ratings');
    }
};
