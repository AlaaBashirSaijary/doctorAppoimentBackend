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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable();
            $table->string('phone_number')->nullable(); // رقم الهاتف
            $table->string('address')->nullable(); // العنوان
           // $table->text('health_status')->nullable();
            $table->text('chronic_diseases')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'phone_number', 'address', 'chronic_diseases']);

        });
    }
};
