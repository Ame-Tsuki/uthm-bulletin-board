<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToAnnouncements extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('announcements', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('announcements', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('announcements', 'featured_order')) {
                $table->integer('featured_order')->default(0);
            }
            if (!Schema::hasColumn('announcements', 'featured_at')) {
                $table->timestamp('featured_at')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'featured_image')) {
                $table->string('featured_image')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'view_count')) {
                $table->integer('view_count')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn([
                'is_active', 'is_featured', 'featured_order', 
                'featured_at', 'featured_image', 'view_count'
            ]);
        });
    }
}