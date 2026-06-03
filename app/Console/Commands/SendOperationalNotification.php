<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\OperationalNotification;
use Illuminate\Support\Facades\Notification;

class SendOperationalNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:operational
                            {title : Notification title}
                            {message : Notification message}
                            {--url=# : Link URL}
                            {--severity=info : Severity (info|warning|critical)}
                            {--audience=all : Audience (all|staff|students)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an operational notification to users';

    public function handle()
    {
        $title = $this->argument('title');
        $message = $this->argument('message');
        $url = $this->option('url') ?? '#';
        $severity = $this->option('severity') ?? 'info';
        $audience = $this->option('audience') ?? 'all';

        if ($audience === 'staff') {
            $users = User::whereIn('role', ['admin', 'staff'])->get();
        } elseif ($audience === 'students') {
            $users = User::whereNotIn('role', ['admin', 'staff'])->get();
        } else {
            $users = User::all();
        }

        if ($users->isEmpty()) {
            $this->info('No users found for the selected audience.');
            return 0;
        }

        Notification::send($users, new OperationalNotification($title, $message, $url, $severity, $audience));
        $this->info('Operational notification sent to ' . $users->count() . ' users.');

        return 0;
    }
}
