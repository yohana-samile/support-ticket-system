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
        return $this->query()->with('topic')->orderBy('created_at', 'DESC')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $topic = $this->query()->create([
                'name' => $data['name'],
                'topic_id' => $data['topic_id'],
                'description' => $data['description'],
                'is_active' => $data['is_active']  ?? true,
            ]);
            return $topic->load('topic');
        });
    }

    public function update($subtopic, array $data)
    {
        $subtopic->update($data);
        activity()
            ->performedOn($subtopic)
            ->causedBy(auth()->user())
            ->withProperties([
                'attributes' => $data,
                'old' => $subtopic->getOriginal()
            ])->log('edit subtopic');
        return $subtopic->fresh();
    }

    public function delete($subtopic)
    {
        return DB::transaction(function () use ($subtopic) {
            activity()
                ->performedOn($subtopic->topic)
                ->causedBy(auth()->user())
                ->withProperties(['sub_topic' => $subtopic->id])
                ->log('deleted subtopic');

            $this->renamingSoftDelete($subtopic, 'name');
            return $subtopic->delete();
        });
    }

    public function getByTopicId($topicId)
    {
        return $this->query()->where('topic_id', $topicId)->orderBy('created_at', 'DESC')->get();
    }

    public function findByUid($topicUid)
    {
        return $this->query()->where('uid', $topicUid)->with('topic')->first();
    }
}
