<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class AddImageColumn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:add-image-column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add image column to announcements table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            if (!Schema::hasColumn('announcements', 'image')) {
                Schema::table('announcements', function ($table) {
                    $table->string('image')->nullable()->after('content')->comment('Path to announcement featured image');
                });
                $this->info('Image column added successfully to announcements table');
            } else {
                $this->info('Image column already exists in announcements table');
            }
        } catch (\Exception $e) {
            $this->error('Error adding image column: ' . $e->getMessage());
        }
    }
}
