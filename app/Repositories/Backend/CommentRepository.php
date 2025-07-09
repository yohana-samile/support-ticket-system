<?php

namespace App\Repositories\Backend;
use App\Models\Access\User;
use App\Models\Comment;
use App\Models\System\CodeValue;
use App\Models\Ticket;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCommentedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketReassignedNotification;
use App\Notifications\TicketUnassignedNotification;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class CommentRepository extends  BaseRepository {
    const MODEL = Comment::class;
    protected $model;

    public function __construct(Comment $comment)
    {
        $this->model = $comment;
    }

    public function store(Ticket $ticket, User $user, array $data): Comment
    {
        return DB::transaction(function () use ($ticket, $user, $data) {
            $comment = $this->model->create([
                'content' => $data['content'],
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
            ]);

            activity()
                ->performedOn($ticket)
                ->causedBy($user)
                ->withProperties(['comment_id' => $comment->id])
                ->log('added comment');

            /**
             * Notify ticket assignee (if different from commenter)
             */
            if ($ticket->assigned_to && $ticket->assigned_to !== $user->id) {
                $ticket->assignee->notify(
                    new TicketCommentedNotification($ticket, $comment, $user)
                );
            }

            return $comment->load('user');
        });
    }

    public function getTicketComments(Ticket $ticket, int $perPage = null)
    {
        $query = $this->model->with('user')
            ->where('ticket_id', $ticket->id)
            ->latest();

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update([
            'content' => $data['content'],
            'edited_at' => now(),
        ]);

        return $comment->fresh();
    }

    public function delete(Comment $comment): bool
    {
        return DB::transaction(function () use ($comment) {
            activity()
                ->performedOn($comment->ticket)
                ->causedBy(auth()->user())
                ->withProperties(['comment_id' => $comment->id])
                ->log('deleted comment');

            return $comment->delete();
        });
    }
}
