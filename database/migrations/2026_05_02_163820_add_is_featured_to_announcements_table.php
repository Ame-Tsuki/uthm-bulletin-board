<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'is_featured')) {
                    $table->boolean('is_featured')->default(false);
                }
                if (!Schema::hasColumn('announcements', 'featured_image')) {
                    $table->string('featured_image')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (Schema::hasColumn('announcements', 'is_featured')) {
                    $table->dropColumn('is_featured');
                }
                if (Schema::hasColumn('announcements', 'featured_image')) {
                    $table->dropColumn('featured_image');
                }
            });
        }
    }
};