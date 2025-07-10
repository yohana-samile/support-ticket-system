<?php

namespace App\Repositories\Frontend;
use App\Models\Access\User;
use App\Models\System\CodeValue;
use App\Models\Ticket;
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
            $status = CodeValue::query()->where('reference', 'CASE01')->value('name');
            $assignedTo = $this->assignedTo();

            $ticket = $this->query()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $data['category_id'],
                'priority' => $data['priority'],
                'status' => $status,
                'user_id' => auth()->id(),
                'reported_by' => auth()->id(),
                'reported_customer' => userFullName(),
                'ticket_number' => Ticket::generateTicketNumber(),
                'assigned_to' => $assignedTo,
                'due_date' => $data['due_date'] ?? null,
            ]);

            $this->notifyTicketCreated(auth()->user(), $ticket);

            if (!empty($assignedTo)) {
                $this->notifyAssignedUser(
                    $assignedTo,
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
            $status = CodeValue::query()->where('reference', 'CASE01')->value('name');

            $ticket->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $data['category_id'],
                'priority' => $data['priority'],
                'status' => $status,
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

    protected function assignedTo()
    {
        return User::query()->where('is_super_admin', true)->inRandomOrder()->value('id');
    }

    protected function notifyTicketCreated(User $user, Ticket $ticket)
    {
        $user->notify(new TicketCreatedNotification($ticket));
    }

    public function getUserTickets($userId, $perPage = 15)
    {
        return $this->ticket->where('user_id', $userId)->with(['category', 'assignedTo'])->latest()->paginate($perPage);
    }
}
