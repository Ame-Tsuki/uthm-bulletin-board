<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'announcement_id')) {
                $table->foreignId('announcement_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('announcements')
                    ->nullOnDelete();

                $table->unique(['user_id', 'announcement_id'], 'events_user_announcement_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'announcement_id')) {
                $table->dropUnique('events_user_announcement_unique');
                $table->dropConstrainedForeignId('announcement_id');
            }
        });
    }
};
