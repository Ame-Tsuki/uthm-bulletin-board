<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddVisibilityAndFixEventsTable extends Migration
{
    public function up()
    {
        // Check if visibility column exists, if not add it
        if (!Schema::hasColumn('events', 'visibility')) {
            Schema::table('events', function (Blueprint $table) {
                $table->enum('visibility', ['private', 'public'])->default('private')->after('type');
            });
        }
        
        // Also ensure the type column has 'important' option
        // This is for MySQL
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('lecture', 'deadline', 'exam', 'social', 'workshop', 'other', 'important') NOT NULL");
    }

    public function down()
    {
        if (Schema::hasColumn('events', 'visibility')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('visibility');
            });
        }
        
        // Revert type enum to original
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('lecture', 'deadline', 'exam', 'social', 'workshop', 'other') NOT NULL");
    }
}