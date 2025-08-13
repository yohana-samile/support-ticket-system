<p>Hello {{ $sticker->creator->name }},</p>
<p>This is a reminder for your sticker:</p>
<p><strong>{{ $sticker->note }}</strong></p>
<p>Reminder set for: {{ $sticker->remind_at->format('M d, Y g:i A') }}</p>
<p>Thank you!</p>
