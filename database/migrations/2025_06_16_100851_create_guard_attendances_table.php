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
        Schema::create('guard_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guard_id');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->boolean('late_arrival')->default(false)->nullable();
            $table->boolean('early_leave')->default(false)->nullable();
            $table->time('total_assigned_time')->nullable();     
            $table->time('total_worked_hours')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half Day'])->default('Present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('guard_id')->references('id')->on('guards')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guard_attendances');
    }
};