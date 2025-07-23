<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendSmsTrait
{
    /**
     * sender_ids required is:-
     * HARUSI    Approved    2025-07-11 14:28:49
     * 2    OTP    Approved    2025-06-30 10:30:29
     * 3    KCP CARD    Approved    2025-06-04 10:39:24
     * 4    REMINDER    Approved    2025-03-26 17:11:52
     * 5    BFPL UHAI    Approved    2025-03-12 12:45:31
     * 6    MICHANGO    Approved    2025-03-07 15:17:25
     * 7    KKKTKILUVYA    Approved    2024-11-06 13:56:36
     * 8    SENDOFF    Approved    2024-09-13 19:15:49
     * 9    HARUSIYANGU    Approved    2021-06-02 13:01:21
     * 10    RMNDR
     */
    public function notifyForNewTicket($user, $ticket)
    {
        $topic = optional($ticket->topic)->name;
        $subtopic = optional($ticket->subtopic)->name;
        $tertiary = optional($ticket->tertiaryTopic)->name;

        $topicLine = implode(' -> ', array_filter([$topic, $subtopic, $tertiary]));
        $message = "{$ticket->ticket_number}\n{$ticket->title}\n{$topicLine}\n{$ticket->description}";

        $smsPayload = [
            'from' => 'REMINDER',
            'to' => $user->phone,
            'text' => $message,
            'reference' => 'xaefcgt'
        ];
//        Log::info('SMS Payload:', $smsPayload);

        $response = Http::withHeaders([
            'Authorization' => 'Basic TmFuYWg6bmFuYWhAMjUxMg==',
        ])->post('https://messaging-service.co.tz/api/sms/v1/text/single', $smsPayload);

        if ($response->successful()) {
            Log::info('SMS sent successfully.');
        } else {
            Log::error('Failed to send SMS.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
