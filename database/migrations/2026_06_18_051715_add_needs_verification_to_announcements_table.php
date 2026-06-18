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
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'needs_verification')) {
                $table->boolean('needs_verification')->default(false);
            }
            if (!Schema::hasColumn('announcements', 'attachments')) {
                $table->text('attachments')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('announcements', 'needs_verification')) {
                $cols[] = 'needs_verification';
            }
            if (Schema::hasColumn('announcements', 'attachments')) {
                $cols[] = 'attachments';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
