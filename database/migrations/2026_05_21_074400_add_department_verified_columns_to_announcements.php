<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'expiry_date')) {
                $table->timestamp('expiry_date')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('announcements', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['department', 'expiry_date', 'verified_at', 'verified_by']);
        });
    }
};