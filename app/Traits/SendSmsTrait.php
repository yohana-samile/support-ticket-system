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

        $this->sendSms($this->buildSmsPayload('REMINDER', $user->phone, $message));
    }

    public function notifyForTicketReassign($user, $ticket, $type)
    {
        $topic = optional($ticket->topic)->name;
        $subtopic = optional($ticket->subtopic)->name;
        $tertiary = optional($ticket->tertiaryTopic)->name;
        $assignTitle = "You have been assigned new ticket";
        $unAssignTitle = "You have been unassigned this ticket";

        $topicLine = implode(' -> ', array_filter([$topic, $subtopic, $tertiary]));


        switch ($type) {
            case 'new_assign':
                $message = "{$assignTitle}\n{$ticket->ticket_number}\n{$ticket->title}\n{$topicLine}\n{$ticket->description}";
                $this->sendSms($this->buildSmsPayload('REMINDER', $user->phone, $message));
                break;
            case 'previous_assign':
                $message = "{$unAssignTitle}\n{$ticket->ticket_number}\n{$ticket->title}\n{$topicLine}\n{$ticket->description}";
                $this->sendSms($this->buildSmsPayload('REMINDER', $user->phone, $message));
                break;
            default:
                abort("no service");
        }
    }

    public function stickerReminderMail($phone, $sticker)
    {
        $this->sendSms($this->buildSmsPayload('REMINDER', $phone, "Reminder set for: " . $sticker->remind_at->format('M d, Y g:i A') . "\nNote: " . $sticker->note));
    }

    private function buildSmsPayload($from, $to, $text, $reference = 'xaefcgt')
    {
        return [
            'from' => $from,
            'to' => $to,
            'text' => $text,
            'reference' => $reference
        ];
    }

    protected function sendSms($smsPayload): void
    {
        $token = 'Basic ' . env('SMS_BASIC_TOKEN');
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://messaging-service.co.tz/api/sms/v1/text/single', $smsPayload);

        if (!$response->successful()) {
            Log::error('Failed to send SMS.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
