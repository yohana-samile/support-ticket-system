<?php

namespace App\Console\Commands;

use App\Models\Ticket\Ticket;
use Illuminate\Console\Command;

class AutoCloseResolvedTickets extends Command
{
    protected $signature = 'tickets:autoclose';
    protected $description = 'Automatically close resolved tickets after 3 days';

    public function handle()
    {
        $tickets = Ticket::where('status', 'resolved')->where('updated_at', '<', now()->subDays(3))->get();

        $tickets->each(function ($ticket) {
            $ticket->update(['status' => 'closed']);
            $this->info("Closed Ticket #{$ticket->ticket_number}");
        });

        $this->info("Total closed: {$tickets->count()}");
    }
}
