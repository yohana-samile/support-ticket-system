<?php

namespace App\Mail;

use App\Models\Sticker;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StickerReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $sticker;
    public function __construct(Sticker $sticker)
    {
        $this->sticker = $sticker;
    }

    public function build()
    {
        return $this->subject('Sticker Reminder')->view('emails.sticker_reminder');
    }
}
