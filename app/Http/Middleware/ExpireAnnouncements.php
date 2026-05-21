<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpireAnnouncements
{
    public function handle(Request $request, Closure $next): Response
    {
        Announcement::expireDueAnnouncements();

        return $next($request);
    }
}
