<?php
// database/migrations/xxxx_add_google_calendar_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add Google Calendar fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable();
            $table->string('google_token')->nullable();
            $table->string('google_refresh_token')->nullable();
            $table->timestamp('google_token_expires_at')->nullable();
            $table->string('google_calendar_id')->nullable();
            $table->boolean('google_calendar_synced')->default(false);
        });

        // Add Google Calendar fields to events table
        Schema::table('events', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->after('visibility');
            $table->boolean('synced_with_google')->default(false)->after('google_event_id');
            $table->timestamp('last_synced_at')->nullable()->after('synced_with_google');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id', 'google_token', 'google_refresh_token',
                'google_token_expires_at', 'google_calendar_id', 
                'google_calendar_synced'
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'synced_with_google', 'last_synced_at']);
        });
    }
};