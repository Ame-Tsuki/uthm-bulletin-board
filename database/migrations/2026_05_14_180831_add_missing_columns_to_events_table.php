<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('events', 'set_reminder')) {
                $table->boolean('set_reminder')->default(false)->after('all_day');
            }
            if (!Schema::hasColumn('events', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('all_day');
            }
            if (!Schema::hasColumn('events', 'recurrence_pattern')) {
                $table->string('recurrence_pattern')->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('events', 'color')) {
                $table->string('color')->nullable()->after('type');
            }
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $columns = ['set_reminder', 'is_recurring', 'recurrence_pattern', 'color'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};