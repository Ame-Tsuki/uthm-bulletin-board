<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('announcements')
            ->where('is_banned', true)
            ->where('status', '!=', 'banned')
            ->update(['status' => 'banned']);
    }

    public function down(): void
    {
        // Cannot reliably restore previous status
    }
};
