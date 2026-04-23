<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateEventsTypeEnumToIncludeImportant extends Migration
{
    public function up()
    {
        // For MySQL
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('lecture', 'deadline', 'exam', 'social', 'workshop', 'other', 'important') NOT NULL");
    }

    public function down()
    {
        // Rollback to original values
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('lecture', 'deadline', 'exam', 'social', 'workshop', 'other') NOT NULL");
    }
}