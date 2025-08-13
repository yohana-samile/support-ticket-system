<?php

namespace App\Jobs;

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

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d H:i');

        $stickers = Sticker::query()
            ->whereNotNull('remind_at')
            ->whereRaw("TO_CHAR(remind_at, 'YYYY-MM-DD HH24:MI') = ?", [$now])->get();
        foreach ($stickers as $sticker) {
            try {
                /**
                 * send email
                 */
                if ($sticker->creator && $sticker->creator->email) {
                    Mail::to($sticker->creator->email)->send(new StickerReminderMail($sticker));
                }

                /**
                 * send sms
                 */
                if ($sticker->creator && $sticker->creator->phone) {
                    $this->stickerReminderMail($sticker->creator->phone, $sticker);
                }

                Log::info("Reminder sent for sticker Id: {$sticker->id}");
            } catch (\Throwable $e) {
                Log::error("Failed to send reminder for sticker ID {$sticker->id}: " . $e->getMessage());
            }
        }
    }
}
