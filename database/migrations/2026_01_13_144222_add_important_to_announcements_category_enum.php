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
        // Modify the category enum to include 'important'
        DB::statement("ALTER TABLE `announcements` MODIFY COLUMN `category` ENUM('academic', 'events', 'general', 'urgent', 'important') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'important' from the enum and convert existing 'important' values to 'general'
        DB::statement("UPDATE `announcements` SET `category` = 'general' WHERE `category` = 'important'");
        DB::statement("ALTER TABLE `announcements` MODIFY COLUMN `category` ENUM('academic', 'events', 'general', 'urgent') NOT NULL");
    }
};
