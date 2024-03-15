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
            $table->unsignedBigInteger('chat_room');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->foreign('chat_room')->references('id')->on('friend_requests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('media_type')->nullable();
            $table->string('media_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
