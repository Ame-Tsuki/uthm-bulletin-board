<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->boolean('moderation_flagged')->default(false)->after('status');
            $table->json('moderation_results')->nullable()->after('moderation_flagged');
            $table->timestamp('moderation_checked_at')->nullable()->after('moderation_results');
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['moderation_flagged', 'moderation_results', 'moderation_checked_at']);
        });
    }
};