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
//        $sessionMessage = "{$ticket->ticket_number}\n{$ticket->title}\n{$topicLine}\n{$ticket->description}";

        /**
        $attributes = [
            [
            "var1" => $user->name
            ]
            ];
         */

        $attributes = [
            [
                "name" => $user->name,
                "ticket_number" => $ticket->ticket_number,
                "title" => $ticket->title,
                "topic" => $topicLine,
                "description" => $ticket->description,
                "priority" => $ticket->priority
            ]
        ];
        $to = [
            (int) $user->phone
        ];
        $template = 'ticket assignment';
        $document = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';

        /**
        if ($ticket->attachments) {
            $document = $this->createDocument64Bit($ticket->attachments);
        }
         */

        $this->sendMessage($this->buildMessagePayload('NEXTBYTE', $to, $template, $attributes, $document));
    }

    protected function createDocument64Bit($document)
    {
        if (!$document) {
            return null;
        }
        $documents = is_array($document) ? $document : [$document];
        $encodedDocuments = [];

        foreach ($documents as $file) {
            if (!file_exists($file)) {
                $filePath = storage_path('app/', ltrim($file, '/'));
            } else {
                $filePath = $file;
            }

            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                $fileContent = file_get_contents($filePath);
                $base64 = base64_decode($fileContent);

                $encodedDocuments[] = [
                    'filename' => basename($filePath),
                    'mime_type' => $mimeType,
                    'base64' => $base64,
                ];
            }
        }

        return count($encodedDocuments) === 1 ? $encodedDocuments[0] : $encodedDocuments;
    }

    private function buildMessagePayload($account, $to, $template, $attributes = null, $document = null, $reference = 'xaefcgt')
    {
        return [
            'to' => $to,
            'account' => $account,
            'template' => $template,
            'attributes' => $attributes,
            'document' => $document,
            'reference' => $reference,
        ];
    }

    protected function sendMessage($payload): void
    {
        $token = 'Bearer ' . env('WHATSAPP_BEARER_TOKEN');
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://messaging-service.co.tz/api/whatsapp/v2/text/single', $payload);

        if (!$response->successful()) {
            Log::error('Failed to send Message.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } else {
            Log::debug('WhatsApp message sent successfully.', [
                'response' => $response->json()
            ]);
        }
    }
}
