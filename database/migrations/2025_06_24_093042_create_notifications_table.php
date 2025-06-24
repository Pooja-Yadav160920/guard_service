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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guard_id');
            $table->string('guard_name')->nullable(); 
            $table->string('type');
            $table->text('message');
            $table->boolean('sent')->default(false);
            $table->timestamp('sent_at')->nullable(); // match your JSON response
            $table->boolean('responded')->default(false);
            $table->timestamp('responded_at')->nullable();
            $table->foreign('guard_id')->references('id')->on('guards')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
