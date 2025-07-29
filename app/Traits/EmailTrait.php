<?php

namespace App\Traits;

use App\Models\Ticket\Ticket;

trait EmailTrait
{
    public function getTicketCategoryPath(Ticket $ticket, string $separator = ' → '): string
    {
        $path = [
            optional($ticket->topic)->name,
            optional($ticket->subtopic)->name,
            optional($ticket->tertiaryTopic)->name
        ];

        return implode($separator, array_filter($path)) ?: 'General';
    }

    public function formatPriority(string $priority): string
    {
        $emoji = [
            'low' => '🔵',
            'medium' => '🟡',
            'high' => '🔴',
            'critical' => '🚨'
        ][strtolower($priority)] ?? '⚪';

        return ucfirst($priority) . " {$emoji}";
    }
}
