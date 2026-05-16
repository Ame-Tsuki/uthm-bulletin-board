<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event as GoogleEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $this->client->setIncludeGrantedScopes(true);
        
        // IMPORTANT: Set ALL required scopes
        $this->client->setScopes([
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ]);
    }

    /**
     * Get authorization URL
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth callback
     */
    public function handleCallback($code)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                Log::error('Google OAuth error: ' . $token['error']);
                return null;
            }

            return $token;
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Set user token
     */
    public function setUserToken(User $user)
    {
        if (!$user->google_token) {
            throw new \Exception('User has no Google token');
        }

        // Calculate a safe "created" timestamp back-calculated from your expires_at column
        $createdTime = $user->google_token_expires_at 
            ? strtotime($user->google_token_expires_at) - 3600 
            : strtotime($user->updated_at);

        $token = [
            'access_token' => $user->google_token,
            'refresh_token' => $user->google_refresh_token,
            'created' => $createdTime,
            'expires_in' => 3600,
        ];

        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            if (!$user->google_refresh_token) {
                throw new \Exception('Refresh token missing');
            }

            $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if (isset($newToken['access_token'])) {
                $user->update([
                    'google_token' => $newToken['access_token'],
                    // Update the real expiration time based on what Google returns
                    'google_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                ]);

                $this->client->setAccessToken($newToken);
            }
        }
    }

    /**
     * Get or create user calendar
     */
    public function getUserCalendar(User $user)
    {
        $this->setUserToken($user);
        $calendarService = new Calendar($this->client);

        if ($user->google_calendar_id) {
            try {
                return $calendarService->calendars->get($user->google_calendar_id);
            } catch (\Exception $e) {
                Log::warning('Calendar not found: ' . $e->getMessage());
            }
        }

        // Create new calendar
        $calendar = new Calendar\Calendar();
        $calendar->setSummary('UTHM Bulletin Board');
        $calendar->setTimeZone('Asia/Kuala_Lumpur');

        $createdCalendar = $calendarService->calendars->insert($calendar);

        $user->update([
            'google_calendar_id' => $createdCalendar->getId(),
            'google_calendar_synced' => true,
        ]);

        return $createdCalendar;
    }

    /**
     * Sync event to Google Calendar
     */
    public function syncEvent(Event $event)
    {
        try {
            $user = User::find($event->user_id);
            
            if (!$user || !$user->google_token) {
                return false;
            }

            $this->setUserToken($user);
            $calendarService = new Calendar($this->client);
            
            // Dynamically target your custom calendar instead of hardcoded 'primary'
            $calendarId = $user->google_calendar_id ?? 'primary';

            $googleEvent = new GoogleEvent();
            $googleEvent->setSummary($event->title);
            $googleEvent->setDescription($event->description);
            $googleEvent->setLocation($event->location);

            // Handle event times safely
            if ($event->all_day) {
                $baseEndDate = $event->end_date ?: $event->start_date;

                $start = new \Google\Service\Calendar\EventDateTime();
                $start->setDate(date('Y-m-d', strtotime($event->start_date)));

                $end = new \Google\Service\Calendar\EventDateTime();
                $end->setDate(date('Y-m-d', strtotime($baseEndDate . ' +1 day')));

                $googleEvent->setStart($start);
                $googleEvent->setEnd($end);
            } else {
                $startDateTime = new \Google\Service\Calendar\EventDateTime();
                $startDateTime->setDateTime(
                    date('c', strtotime($event->start_date . ' ' . ($event->start_time ?? '00:00:00')))
                );
                $startDateTime->setTimeZone('Asia/Kuala_Lumpur');
                
                $endDateTime = new \Google\Service\Calendar\EventDateTime();
                $endDateTime->setDateTime(
                    date('c', strtotime(($event->end_date ?? $event->start_date) . ' ' . ($event->end_time ?? '23:59:59')))
                );
                $endDateTime->setTimeZone('Asia/Kuala_Lumpur');
                
                $googleEvent->setStart($startDateTime);
                $googleEvent->setEnd($endDateTime);
            }

            // Insert or Update inside Google Calendar API
            if ($event->google_event_id) {
                $googleEvent = $calendarService->events->update(
                    $calendarId, 
                    $event->google_event_id, 
                    $googleEvent
                );
            } else {
                $googleEvent = $calendarService->events->insert($calendarId, $googleEvent);
                
                Log::info('Google Event Created', [
                    'event_id' => $googleEvent->getId(),
                    'link' => $googleEvent->getHtmlLink(),
                ]);

                $event->update([
                    'google_event_id' => $googleEvent->getId(),
                    'synced_with_google' => true,
                    'last_synced_at' => now(),
                ]);
            }

            return $googleEvent;

        } catch (\Exception $e) {
            Log::error('Google Calendar sync error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete event from Google Calendar
     */
    public function deleteEvent(Event $event)
    {
        try {
            // Note: Changed from $event->creator to standard user_id relationship matching syncEvent
            $user = User::find($event->user_id);
            
            if (!$user || !$user->google_token || !$event->google_event_id) {
                return false;
            }

            $this->setUserToken($user);
            $calendarService = new Calendar($this->client);
            
            // FIX: Use the custom calendar ID instead of hardcoded 'primary'
            $calendarId = $user->google_calendar_id ?? 'primary';

            $calendarService->events->delete($calendarId, $event->google_event_id);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync events from Google Calendar
     */
    public function syncFromGoogle(User $user)
    {
        try {
            $this->setUserToken($user);
            $calendarService = new Calendar($this->client);
            
            // FIX: Pull details from custom board instead of personal 'primary' list
            $calendarId = $user->google_calendar_id ?? 'primary';

            if (!$calendarId) {
                return [];
            }

            $optParams = [
                'updatedMin' => now()->subDays(30)->toRfc3339String(),
                'maxResults' => 100,
                'orderBy' => 'updated',
                'showDeleted' => true,
                'singleEvents' => true, // Recommended to expand recurring occurrences cleanly
            ];

            $googleEvents = $calendarService->events->listEvents($calendarId, $optParams);
            $syncedEvents = [];

            foreach ($googleEvents->getItems() as $googleEvent) {
                if ($googleEvent->getStatus() === 'cancelled') {
                    Event::where('google_event_id', $googleEvent->getId())->delete();
                    continue;
                }

                $localEvent = Event::where('google_event_id', $googleEvent->getId())->first();

                if (!$localEvent) {
                    $localEvent = Event::create([
                        'user_id' => $user->id,
                        'title' => $googleEvent->getSummary() ?? 'Untitled Event',
                        'description' => $googleEvent->getDescription(),
                        'location' => $googleEvent->getLocation(),
                        'start_date' => $this->parseGoogleDate($googleEvent->getStart()),
                        'end_date' => $this->parseGoogleDate($googleEvent->getEnd()),
                        'type' => 'other',
                        'visibility' => 'private',
                        'google_event_id' => $googleEvent->getId(),
                        'synced_with_google' => true,
                        'last_synced_at' => now(),
                    ]);
                }

                $syncedEvents[] = $localEvent;
            }

            return $syncedEvents;
        } catch (\Exception $e) {
            Log::error('Sync from Google error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Disconnect Google Calendar
     */
    public function disconnect(User $user)
    {
        try {
            if ($user->google_token) {
                $this->client->revokeToken();
            }
        } catch (\Exception $e) {
            Log::error('Revoke token error: ' . $e->getMessage());
        }

        $user->update([
            'google_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
            'google_calendar_id' => null,
            'google_calendar_synced' => false,
        ]);
    }

    /**
     * Parse Google date
     */
    protected function parseGoogleDate($googleDateTime)
    {
        if ($googleDateTime->getDateTime()) {
            return date('Y-m-d', strtotime($googleDateTime->getDateTime()));
        } elseif ($googleDateTime->getDate()) {
            return $googleDateTime->getDate();
        }
        return date('Y-m-d');
    }
}