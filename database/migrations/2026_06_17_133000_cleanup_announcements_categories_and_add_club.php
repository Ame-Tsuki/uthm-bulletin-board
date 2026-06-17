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
        // 1. Data Migration: Update any existing category values
        // For announcements with category = 'urgent', set category = 'general' and priority = 'urgent'
        DB::table('announcements')
            ->where('category', 'urgent')
            ->update([
                'category' => 'general',
                'priority' => 'urgent'
            ]);

        // For announcements with category = 'important', set category = 'general' and priority = 'important'
        DB::table('announcements')
            ->where('category', 'important')
            ->update([
                'category' => 'general',
                'priority' => 'important'
            ]);

        // 2. Modify enum column (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `announcements` MODIFY COLUMN `category` ENUM('academic', 'events', 'general', 'club') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `announcements` MODIFY COLUMN `category` ENUM('academic', 'events', 'general', 'club', 'urgent', 'important') NOT NULL");
        }
    }
};
