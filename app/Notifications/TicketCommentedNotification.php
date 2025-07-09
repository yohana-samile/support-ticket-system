<?php

namespace App\Notifications;

use App\Models\Access\User;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $comment;
    public $commenter;

    public function __construct(Ticket $ticket, Comment $comment, User $commenter)
    {
        $this->ticket = $ticket;
        $this->comment = $comment;
        $this->commenter = $commenter;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Comment on Ticket: ' . $this->ticket->title)
            ->line($this->commenter->name . ' added a comment to your assigned ticket')
            ->action('View Ticket', route('tickets.show', $this->ticket->id))
            ->line('Comment:')
            ->line($this->comment->content);
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->commenter->name,
            'message' => 'added a comment to your assigned ticket',
            'url' => route('tickets.show', $this->ticket->id),
        ];
    }
}
