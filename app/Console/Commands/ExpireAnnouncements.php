<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;

class ExpireAnnouncements extends Command
{
    protected $signature = 'announcements:expire';

    protected $description = 'Mark published announcements as expired when their expiry date has passed';

    public function handle(): int
    {
        $count = Announcement::expireDueAnnouncements();

        $this->info("Expired {$count} announcement(s).");

        return self::SUCCESS;
    }
}
