<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'UTHM Bulletin Board'],
            ['key' => 'app_description', 'value' => 'Official announcements and events platform'],
            ['key' => 'announcements_per_page', 'value' => '15'],
            ['key' => 'maintenance_mode', 'value' => 'false'],
            ['key' => 'enable_comments', 'value' => 'true'],
            ['key' => 'max_upload_size', 'value' => '5242880'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
