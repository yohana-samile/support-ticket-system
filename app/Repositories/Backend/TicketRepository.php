<?php

namespace App\Repositories\Backend;
use App\Models\Access\Client;
use App\Models\Access\User;
use App\Models\SubTopic;
use App\Models\System\CodeValue;
use App\Models\TertiaryTopic;
use App\Models\Ticket\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\Topic;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketReassignedNotification;
use App\Notifications\TicketUnassignedNotification;
use App\Repositories\BaseRepository;
use App\Traits\SendSmsTrait;
use App\Traits\WhatsAppTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class TicketRepository extends  BaseRepository {
    use SendSmsTrait, WhatsAppTrait;
    const MODEL = Ticket::class;
    protected $ticket;
    protected $user;

    public function __construct(Ticket $ticket, User $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function all()
    {
        return $this->query()->with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo'])->orderBy('created_at', 'DESC');
    }

    public function find($ticketUid)
    {
        return $this->query()->with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo', 'comments.user', 'attachments', 'operators'])->where('uid', $ticketUid)->first();
    }

    public function store(array $data) {
        return DB::transaction(function() use($data) {
            $status = CodeValue::query()->where('reference', 'STATUS01')->value('name');
            if (isset($data['client_name'])) {
                $client = Client::query()->create([
                    "name" =>  $data['client_name'],
                   "email" =>  fake()->email,
                   "saas_app_id" =>  $data['saas_app_id'],
                ]);
               $clientId = $client->id;
            } else {
                $clientId = $data['client_id'];
            }

            $topicName = optional(Topic::query()->find($data['topic_id']))->name;
            $subtopicName = optional(SubTopic::query()->find($data['sub_topic_id']))->name;
            if (isset($data['tertiary_topic_id'])) {
                $tertiaryTopicName = optional(TertiaryTopic::query()->find($data['tertiary_topic_id']))->name;
            }

            if (empty($data['title'])) {
                $data['title'] = "Issues regarding {$topicName}"
                    . (!empty($subtopicName) ? " - {$subtopicName}" : '')
                    . (!empty($tertiaryTopicName) ? " - {$tertiaryTopicName}" : '');
            }
            if (empty($data['description'])) {
                $data['description'] = "This ticket was created to address an issue related to {$topicName}"
                    . (!empty($subtopicName) ? ", specifically about {$subtopicName}" : '')
                    . (!empty($tertiaryTopicName) ? ", and more specifically on {$tertiaryTopicName}" : '') . ".";
            }

            $ticket = $this->query()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'topic_id' => $data['topic_id'],
                'sub_topic_id' => $data['sub_topic_id'],
                'tertiary_topic_id' => $data['tertiary_topic_id'] ?? null,
                'payment_channel_id' => $data['payment_channel_id'] ?? null,
                'sender_id' => $data['sender_id'] ?? null,
                'issue_date' => $data['issue_date'] ?? null,
                'priority' => $data['priority'],
                'status' => $status,
                'user_id' => auth()->id(),
                'saas_app_id' => $data['saas_app_id'],
                'client_id' => $clientId,
                'ticket_number' => Ticket::generateTicketNumber(),
                'assigned_to' => $data['assigned_to'] ?? null,
            ]);

            $this->updateTicketStatusHistory(
                $ticket,
                $status,
                auth()->user()
            );

            $this->notifyTicketCreated(Client::find($clientId), $ticket);

            if (!empty($data['assigned_to'])) {
                $this->notifyAssignedUser(
                    $data['assigned_to'],
                    $ticket,
                    is_array($data['notification_channels'] ?? null)
                        ? $data['notification_channels']
                        : explode(',', $data['notification_channels'] ?? 'mail,database')
                );
            }

            if (!empty($data['operator'])) {
                $ticket->operators()->syncWithoutDetaching($data['operator']);
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
            $originalStatus = $ticket->status;

            $ticket->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $data['category_id'],
                'priority' => $data['priority'],
                'status' => $data['status'] ?? $originalStatus,
                'assigned_to' => $data['assigned_to'] ?? $ticket->assigned_to,
            ]);

            if (isset($data['status'])) {
                $this->updateTicketStatusHistory(
                    $ticket,
                    $data['status'],
                    auth()->user()
                );
            }

            if (isset($data['attachments'])) {
                $this->storeAttachments($ticket, $data['attachments']);
            }

            return $ticket;
        });
    }

    protected function updateTicketStatusHistory(Ticket $ticket, string $newStatus, User $changedBy)
    {
        if ($ticket->status !== $newStatus) {
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'from_status' => $ticket->status,
                'to_status' => $newStatus,
                'changed_by' => $changedBy->id,
                'changed_at' => now(),
            ]);
        }
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
                'response_time' => (int) round($this->calculateResponseTime($ticket)),
            ]);
        }

        $this->updateTicketStatusHistory(
            $ticket,
            $status,
            auth()->user()
        );

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
            $previousAssignee = $ticket->assignedTo;

            $ticket->update(['assigned_to' => $assigneeId]);
            $ticket->refresh();

            $newAssignee = $ticket->assignedTo;
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
            ])->log('reassigned ticket');
    }

    protected function handleReassignmentNotifications(Ticket $ticket, ?User $previousAssignee, ?User $newAssignee): void
    {
        /**
         * Notify new assignee if changed
         */
        if ($newAssignee && (!$previousAssignee || $newAssignee->id !== $previousAssignee->id)) {
            $newAssignee->notify(new TicketReassignedNotification($ticket));
            $this->notifyForTicketReassign($newAssignee, $ticket, 'new_assign');
        }

        /**
         * Notify previous assignee if they were removed
         */
        if ($previousAssignee && (!$newAssignee || $newAssignee->id !== $previousAssignee->id)) {
            $previousAssignee->notify(new TicketUnassignedNotification($ticket));
            $this->notifyForTicketReassign($previousAssignee, $ticket, 'previous_assign');
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
        $user = User::query()->findOrFail($userId);

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'mail':
                    $user->notify(new TicketAssignedNotification($ticket));
                    break;
                case 'database':
                    $user->notify(new TicketAssignedNotification($ticket, false));
                    break;
                case 'sms':
                    $this->notifyForNewTicket($user, $ticket);
                    break;
                case 'whatsapp':
                    $this->sendTicketNotification($user, $ticket);
                    break;
            }
        }
    }

    protected function notifyTicketCreated(Client $client, Ticket $ticket)
    {
        $client->notify(new TicketCreatedNotification($ticket));
    }

    public function getUserTickets($userId)
    {
        return $this->ticket->where('client_id', $userId)->with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'assignedTo', 'operators'])->latest()->paginate();
    }

    public function getAssignedTickets($userId, $perPage = 15)
    {
        return $this->query()->where('assigned_to', $userId)->with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'operators'])->latest()->paginate($perPage);
    }
}
