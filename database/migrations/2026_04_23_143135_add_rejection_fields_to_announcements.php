<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionFieldsToAnnouncements extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('announcements', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('announcements', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->constrained('users')->after('rejected_at');
            }
            if (!Schema::hasColumn('announcements', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->after('verified_by');
            }
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejected_at', 'rejected_by', 'approved_by']);
        });
    }
}