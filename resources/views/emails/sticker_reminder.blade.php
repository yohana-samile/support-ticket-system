<p>Hello {{ $user }},</p>
<p>This is a reminder for your sticker:</p>
<p><strong> {!! Purifier::clean($sticker->note) !!}</strong></p>
<p>Reminder set for: {{ $sticker->remind_at->format('M d, Y g:i A') }}</p>
<p>Thank you!</p>
