<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait WhatsAppTrait
{
    public function sendTicketNotification($user, $ticket)
    {
        $topic = optional($ticket->topic)->name;
        $subtopic = optional($ticket->subtopic)->name;
        $tertiary = optional($ticket->tertiaryTopic)->name;
        $topicLine = implode(' -> ', array_filter([$topic, $subtopic, $tertiary]));
        $sessionMessage = "{$ticket->ticket_number}\n{$ticket->title}\n{$topicLine}\n{$ticket->description}";

        $attributes = [
            "name" => $user->name
        ];

        //"document": "https://www.gstatic.com/webp/gallery/1.png",
        $this->sendMessage($this->buildMessagePayload('TICKETING_SYSTEM', $user->phone, $sessionMessage, $attributes));
    }

    private function buildMessagePayload($account, $to, $sessionMessage, $attributes = null, $document = null, $reference = 'xaefcgt')
    {
        return [
            "data" => [
                'to' => $to,
                'account' => $account,
                'template' => $sessionMessage,
                'attributes' => $attributes,
                'document' => $document,
                'reference' => $reference
            ]
        ];
    }

    protected function sendMessage($payload): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic TmFuYWg6bmFuYWhAMjUxMg==',
        ])->post('https://messaging-service.co.tz/api/whatsapp/v2/text/single', $payload);

        if (!$response->successful()) {
            Log::error('Failed to send Message.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
