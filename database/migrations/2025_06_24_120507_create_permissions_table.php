<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT and primary key
            $table->string('name', 100);
            $table->string('title', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

            // Insert initial seed data
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'Add Role', 'title' => 'add-role'],
            ['id' => 2, 'name' => 'Edit Role', 'title' => 'edit-role'],
            ['id' => 3, 'name' => 'Delete Role', 'title' => 'delete-role'],
            ['id' => 4, 'name' => 'Show Role', 'title' => 'show-role'],
            ['id' => 5, 'name' => 'Add Shift', 'title' => 'add-shift'],
            ['id' => 6, 'name' => 'Edit Shift', 'title' => 'edit-shift'],
            ['id' => 7, 'name' => 'Delete Shift', 'title' => 'delete-shift'],
            ['id' => 8, 'name' => 'Show Shift', 'title' => 'show-shift'],
            ['id' => 9, 'name' => 'Add Location', 'title' => 'add-location'],
            ['id' => 10, 'name' => 'Edit Location', 'title' => 'edit-location'],
            ['id' => 11, 'name' => 'Delete Location', 'title' => 'delete-location'],
            ['id' => 12, 'name' => 'Show Location', 'title' => 'show-location'],
            ['id' => 13, 'name' => 'Add Guard', 'title' => 'add-guard'],
            ['id' => 14, 'name' => 'Edit Guard', 'title' => 'edit-guard'],
            ['id' => 15, 'name' => 'Delete Guard', 'title' => 'delete-guard'],
            ['id' => 16, 'name' => 'Show Guard', 'title' => 'show-guard'],
            ['id' => 17, 'name' => 'Guard Clock In', 'title' => 'guard-clockin'],
            ['id' => 18, 'name' => 'Guard Clock Out', 'title' => 'guard-clockout'],
            ['id' => 19, 'name' => 'Show Attendance', 'title' => 'show-attendance'],
            ['id' => 20, 'name' => 'Add Permission', 'title' => 'add-permission'],
            ['id' => 21, 'name' => 'Edit Permission', 'title' => 'edit-permission'],
            ['id' => 22, 'name' => 'Add Notification', 'title' => 'add-notification'],
            ['id' => 23, 'name' => 'Add User', 'title' => 'add-user'],
            ['id' => 24, 'name' => 'Edit User', 'title' => 'edit-user'],
            ['id' => 25, 'name' => 'Delete User', 'title' => 'delete-user'],
            ['id' => 26, 'name' => 'Show User', 'title' => 'show-user'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
