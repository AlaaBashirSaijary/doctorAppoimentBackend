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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // المرسل
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade'); // المستقبل (إذا كانت محادثة خاصة)
            $table->foreignId('room_id')->nullable()->constrained('chat_rooms')->onDelete('cascade'); // الغرفة للدردشات الجماعية
            $table->text('message')->nullable(); // محتوى الرسالة
            $table->json('attachments')->nullable(); // دعم عدة مرفقات
            $table->string('message_type')->default('text'); // نوع الرسالة
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent'); // حالة الرسالة
            $table->boolean('is_edited')->default(false); // هل تم تعديل الرسالة؟
            $table->timestamp('edited_at')->nullable(); // وقت آخر تعديل
            $table->timestamp('delivered_at')->nullable(); // وقت تسليم الرسالة
            $table->timestamp('received_at')->nullable(); // وقت استلام المستقبل لها
            $table->timestamps(); // يحوي created_at و updated_at
            $table->softDeletes(); // حذف ناعم للرسائل

            // تحسين الأداء باستخدام الفهارس (Indexing)
            $table->index('room_id');
            $table->index('sender_id');
            $table->index('receiver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
