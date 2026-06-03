<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class MailHelper
{
    public static function send($to, $subject, $htmlContent, $fromName = null)
    {
        $apiKey = env('BREVO_API_KEY');
        $fromEmail = env('MAIL_FROM_ADDRESS', 'kuuhakuxblank09@gmail.com');
        $fromName = $fromName ?? env('MAIL_FROM_NAME', 'UTHM Bulletin Board');
        
        // Make sure API key is not empty
        if (empty($apiKey)) {
            \Illuminate\Support\Facades\Log::error('BREVO_API_KEY is empty in .env');
            return false;
        }
        
        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => $fromEmail,
                'name' => $fromName
            ],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ]);
        
        \Illuminate\Support\Facades\Log::info('Brevo API Response', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        return $response->status() === 201 || $response->status() === 202;
    }
}
