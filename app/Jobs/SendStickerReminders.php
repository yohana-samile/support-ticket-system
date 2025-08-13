<?php

namespace App\Jobs;

use App\Constants\NotificationConstants;
use App\Mail\StickerReminderMail;
use App\Models\Sticker;
use App\Traits\SendSmsTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendStickerReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SendSmsTrait;

    /**
     * Execute the job.
     */
    public function handle()
    {
        $now = Carbon::now()->format('YYYY-MM-DD HH24:MI');

        $stickers = Sticker::query()
            ->whereNotNull('remind_at')
            ->whereRaw("TO_CHAR(remind_at, 'YYYY-MM-DD HH24:MI') = ?", [$now])
            ->get();

        foreach ($stickers as $sticker) {
            if ($sticker->is_private) {
                $recipients = collect([$sticker->creator])->filter(); // only creator
            } else {
                $recipients = $sticker->recipients->push($sticker->creator)->filter(); // recipients + creator
            }

            foreach ($recipients as $user) {
                try {
                    $settings = $user->setting;
                    $channel = $settings?->notification_channel ?? NotificationConstants::CHANNEL_BOTH;

                    // send email
                    if (in_array($channel, [NotificationConstants::CHANNEL_EMAIL, NotificationConstants::CHANNEL_BOTH])) {
                        if ($user->email) {
                            Mail::to($user->email)->send(new StickerReminderMail($sticker));
                        }
                    }

                    // send SMS
                    if (in_array($channel, [NotificationConstants::CHANNEL_SMS, NotificationConstants::CHANNEL_BOTH])) {
                        if ($user->phone) {
                            $this->stickerReminderMail($user->phone, $sticker);
                        }
                    }

                    Log::info("Reminder sent for sticker ID {$sticker->id} to user {$user->id} via {$channel}");

                } catch (\Throwable $e) {
                    Log::error("Failed to send reminder for sticker ID {$sticker->id} to user {$user->id}: " . $e->getMessage());
                }
            }
        }
    }
}
