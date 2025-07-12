<?php

namespace App\Repositories\Backend;
use App\Models\Access\User;
use App\Models\SubTopic;
use App\Models\System\CodeValue;
use App\Models\Ticket\Ticket;
use App\Models\Topic;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketReassignedNotification;
use App\Notifications\TicketUnassignedNotification;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class SubTopicRepository extends  BaseRepository {
    const MODEL = SubTopic::class;
    public function getAll()
    {
        return $this->query()::with('saasApp')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $topic = $this->query()->create([
                'name' => $data['name'],
                'saas_app_id' => $data['saas_app_id'],
                'uid' => Str::uuid()
            ]);
            return $topic->load('service');
        });
    }

    public function update($subTopicUid, array $data): ?Subtopic
    {
        $subtopic = $this->findByUid($subTopicUid);
        if ($subtopic) {
            $subtopic->update($data);
            return $subtopic->fresh();
        }
        activity()
            ->performedOn($subtopic->subtopic)
            ->causedBy(auth()->user())
            ->withProperties(['saas_app_id' => $subtopic->id])
            ->log('edit subtopic');

        return null;
    }

    public function delete(Topic $subTopicUid): bool
    {
        $subtopic = $this->findByUid($subTopicUid);
        if (!$subtopic) {
            return false;
        }

        return DB::transaction(function () use ($subtopic) {
            activity()
                ->performedOn($subtopic->topic)
                ->causedBy(auth()->user())
                ->withProperties(['saas_app_id' => $subtopic->id])
                ->log('deleted topic');

            return $subtopic->delete();
        });
    }
    public function getByTopicId($topicId)
    {
        return $this->query()->where('topic_id', $topicId)->orderBy('created_at')->get();
    }

    public function findByUid($topicUid)
    {
        return $this->query()->where('uid', $topicUid)->with('saasApp')->first();
    }
}
