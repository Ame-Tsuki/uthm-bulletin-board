<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Command
{
    protected $signature = 'app:create-settings-table';
    protected $description = 'Create the settings table if it does not exist';

    public function handle()
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
            });
            
            $this->info('Settings table created successfully.');
        } else {
            $this->info('Settings table already exists.');
        }
    }
}
