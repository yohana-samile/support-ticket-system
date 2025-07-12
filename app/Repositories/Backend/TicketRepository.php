<?php

namespace App\Repositories\Backend;
use App\Models\Access\User;
use App\Models\System\CodeValue;
use App\Models\Ticket\Ticket;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketReassignedNotification;
use App\Notifications\TicketUnassignedNotification;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class TicketRepository extends  BaseRepository {
    const MODEL = Ticket::class;
    protected $ticket;
    protected $user;

    public function __construct(Ticket $ticket, User $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function all($perPage = 10)
    {
        return $this->query()->with(['category', 'user', 'assignedTo'])->latest()->paginate($perPage);
    }

    public function find($ticketUid)
    {
        return $this->query()->with(['category', 'user', 'assignedTo', 'comments.user', 'attachments'])->where('uid', $ticketUid)->first();
    }

    public function store(array $data) {
        return DB::transaction(function() use($data) {
            $status = CodeValue::query()->where('reference', 'STATUS01')->value('name');
            $ticket = $this->query()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'topic_id' => $data['topic_id'],
                'sub_topic_id' => $data['sub_topic_id'],
                'tertiary_topic_id' => $data['tertiary_topic_id'],
                'payment_channel_id' => $data['payment_channel_id'] ?? null,
                'sender_id' => $data['sender_id'] ?? null,
                'priority' => $data['priority'],
                'status' => $status,
                'user_id' => auth()->id(),
                'saas_app_id' => $data['saas_app_id'],
                'client_id' => $data['client_id'],
                'ticket_number' => Ticket::generateTicketNumber(),
                'assigned_to' => $data['assigned_to'] ?? null,
            ]);

            $this->notifyTicketCreated(auth()->user(), $ticket);

            if (!empty($data['assigned_to'])) {
                $this->notifyAssignedUser(
                    $data['assigned_to'],
                    $ticket,
                    is_array($data['notification_channels'] ?? null)
                        ? $data['notification_channels']
                        : explode(',', $data['notification_channels'] ?? 'mail,database')
                );
            }

            if (isset($data['attachments'])) {
                $this->storeAttachments($ticket, $data['attachments']);
            }
            return $ticket;
        });
    }

    public function update($uid, array $data)
    {
        return DB::transaction(function () use ($uid, $data) {
            $ticket = $this->find($uid);

            $ticket->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $data['category_id'],
                'priority' => $data['priority'],
                'status' => $data['status'] ?? $ticket->status,
                'assigned_to' => $data['assigned_to'] ?? $ticket->assigned_to,
                'due_date' => $data['due_date'] ?? $ticket->due_date,
            ]);

            if (isset($data['attachments'])) {
                $this->storeAttachments($ticket, $data['attachments']);
            }

            return $ticket;
        });
    }

    public function delete($ticketUid) {
        $ticket = $this->find($ticketUid);
        foreach ($ticket->attachments as $attachment) {
            Storage::delete($attachment->path);
            $attachment->delete();
        }
        return $ticket->delete();
    }

    public function updateStatus($ticketUid, $status)
    {
        $ticket = $this->find($ticketUid);

        $updateData = ['status' => $status];

        if ($status === 'resolved') {
            $updateData = array_merge($updateData, [
                'time_solved' => now(),
                'response_time' => $this->calculateResponseTime($ticket),
            ]);
        }

        /**
         * Reset time_solved if ticket is reopened
         */
        if ($status === 'reopened') {
            $updateData['time_solved'] = null;
            $updateData['response_time'] = null;
        }

        $ticket->update($updateData);

        return $ticket;
    }

    protected function storeAttachments($ticket, $attachments)
    {
        foreach ($attachments as $file) {
            $path = $file->store('attachments/' . $ticket->id);

            $ticket->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }


    public function reassign(Ticket $ticket, ?int $assigneeId, User $reassignedBy)
    {
        return DB::transaction(function () use ($ticket, $assigneeId, $reassignedBy) {
            $previousAssignee = $ticket->assignee;
            $newAssignee = $assigneeId ? User::find($assigneeId) : null;

            $ticket->update(['assigned_to' => $assigneeId]);

            $this->logReassignment($ticket, $reassignedBy, $previousAssignee, $newAssignee);
            $this->handleReassignmentNotifications($ticket, $previousAssignee, $newAssignee);

            return $ticket;
        });
    }

    protected function logReassignment(Ticket $ticket, User $causer, ?User $previousAssignee, ?User $newAssignee): Activity
    {
        return activity()
            ->performedOn($ticket)
            ->causedBy($causer)
            ->withProperties([
                'previous_assignee' => $previousAssignee ? $previousAssignee->name : 'Unassigned',
                'new_assignee' => $newAssignee ? $newAssignee->name : 'Unassigned'
            ])
            ->log('reassigned ticket');
    }

    protected function handleReassignmentNotifications(Ticket $ticket, ?User $previousAssignee, ?User $newAssignee): void
    {
        // Notify new assignee if changed
        if ($newAssignee && (!$previousAssignee || $newAssignee->id !== $previousAssignee->id)) {
            $newAssignee->notify(new TicketReassignedNotification($ticket));
        }

        // Notify previous assignee if they were removed
        if ($previousAssignee && (!$newAssignee || $newAssignee->id !== $previousAssignee->id)) {
            $previousAssignee->notify(new TicketUnassignedNotification($ticket));
        }
    }

    protected function calculateResponseTime($ticket)
    {
        if (!$ticket->time_reported || !$ticket->time_solved) {
            return 0;
        }

        if ($ticket->time_solved < $ticket->time_reported) {
            return 0;
        }
        return abs($ticket->time_reported->diffInHours($ticket->time_solved));
    }

    protected function notifyAssignedUser($userId, Ticket $ticket, $channels = ['mail', 'database'])
    {
        $user = User::findOrFail($userId);

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'mail':
                    $user->notify(new TicketAssignedNotification($ticket));
                    break;

                case 'database':
                    $user->notify(new TicketAssignedNotification($ticket, false));
                    break;

                case 'sms':
                    //TODO Implement SMS notification
                    break;
                case 'whatsapp':
                    //TODO Implement whatsapp notification
                    break;
            }
        }
    }

    protected function notifyTicketCreated(User $user, Ticket $ticket)
    {
        $user->notify(new TicketCreatedNotification($ticket));
    }

    public function getUserTickets($userId, $perPage = 15)
    {
        return $this->ticket->where('user_id', $userId)->with(['category', 'assignedTo'])->latest()->paginate($perPage);
    }

    public function getAssignedTickets($userId, $perPage = 15)
    {
        return $this->query()->where('assigned_to', $userId)
            ->with(['category', 'user'])
            ->latest()
            ->paginate($perPage);
    }
}
