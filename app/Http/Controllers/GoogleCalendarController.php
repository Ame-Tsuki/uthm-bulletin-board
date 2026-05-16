<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    protected $googleService;

    public function __construct()
    {
        // Only instantiate if Google API is configured
        if (config('services.google.client_id')) {
            $this->googleService = new GoogleCalendarService();
        }
    }

    public function connect()
    {
        if (!$this->googleService) {
            return response()->json([
                'success' => false,
                'message' => 'Google Calendar API not configured'
            ], 500);
        }

        try {
            $authUrl = $this->googleService->getAuthUrl();
            return response()->json([
                'success' => true,
                'auth_url' => $authUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Google connect error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->get('code');
            
            if (!$code || !$this->googleService) {
                return redirect()->route('calendar')->with('error', 'Authorization failed');
            }

            $token = $this->googleService->handleCallback($code);
            
            if (!$token) {
                return redirect()->route('calendar')->with('error', 'Failed to get access token');
            }

            $user = Auth::user();
            
            $user->update([
                'google_token' => $token['access_token'] ?? null,
                'google_refresh_token' => $token['refresh_token'] ?? null,
                'google_token_expires_at' => isset($token['expires_in']) ? now()->addSeconds($token['expires_in']) : null,
            ]);

            // Set up user's calendar after successful auth
            try {
                $calendar = $this->googleService->getUserCalendar($user);
            } catch (\Exception $e) {
                Log::warning('Failed to set up calendar: ' . $e->getMessage());
            }

            return redirect()->route('calendar')->with('success', 'Google Calendar connected!');
        } catch (\Exception $e) {
            Log::error('Callback error: ' . $e->getMessage());
            return redirect()->route('calendar')->with('error', 'Failed to connect Google Calendar');
        }
    }

    public function disconnect()
    {
        try {
            $user = Auth::user();
            
            if ($this->googleService) {
                $this->googleService->disconnect($user);
            } else {
                $user->update([
                    'google_token' => null,
                    'google_refresh_token' => null,
                    'google_calendar_id' => null,
                    'google_calendar_synced' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Google Calendar disconnected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function status()
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'connected' => !is_null($user->google_token),
                'synced' => $user->google_calendar_synced ?? false,
                'calendar_id' => $user->google_calendar_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'synced' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function createEvent($user, $localEvent)
{
    $client = $this->getClient($user);

    $service = new \Google_Service_Calendar($client);

    $event = new \Google_Service_Calendar_Event([
        'summary' => $localEvent->title,

        'description' => $localEvent->description,

        'start' => [
            'dateTime' => \Carbon\Carbon::parse($localEvent->start_time)
                ->toRfc3339String(),

            'timeZone' => 'Asia/Kuala_Lumpur',
        ],

        'end' => [
            'dateTime' => \Carbon\Carbon::parse($localEvent->end_time)
                ->toRfc3339String(),

            'timeZone' => 'Asia/Kuala_Lumpur',
        ],
    ]);

    $calendarId = 'primary';

    $googleEvent = $service->events->insert($calendarId, $event);

    return $googleEvent;
}

    public function sync()
    {
        try {
            $user = Auth::user();
            
            if (!$user->google_token || !$this->googleService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Calendar not connected'
                ], 400);
            }

            $syncedEvents = $this->googleService->syncFromGoogle($user);

            return response()->json([
                'success' => true,
                'message' => 'Calendar synced successfully',
                'synced_count' => count($syncedEvents)
            ]);
        } catch (\Exception $e) {
            Log::error('Sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}