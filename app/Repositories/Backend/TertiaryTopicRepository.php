<?php

namespace App\Repositories\Backend;
use App\Models\TertiaryTopic;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class TertiaryTopicRepository extends  BaseRepository {

    const MODEL = TertiaryTopic::class;

    public function getAll()
    {
        return $this->query()->with('subtopic')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tertiaryTopic = $this->query()->create($data);
            return $tertiaryTopic->load('subtopic');
        });
    }

    public function update(TertiaryTopic $tertiaryTopic, array $data): TertiaryTopic
    {
        DB::transaction(function () use ($tertiaryTopic, $data) {
            $tertiaryTopic->update($data);
        });
        return $tertiaryTopic->fresh();
    }

    public function findByUid($tertiaryTopicUid)
    {
        return $this->query()->where('uid', $tertiaryTopicUid)->with('subtopic')->first();
    }
    public function delete(TertiaryTopic $tertiaryTopic): bool
    {
        return DB::transaction(function () use ($tertiaryTopic) {
            activity()
                ->performedOn($tertiaryTopic)
                ->causedBy(auth()->user())
                ->withProperties(['tertiary_topic_id' => $tertiaryTopic->id])
                ->log('deleted tertiary topic');

            return $tertiaryTopic->delete();
        });
    }

    public function getBySubtopicId($subtopicId)
    {
        return $this->query()->where('sub_topic_id', $subtopicId)->orderBy('created_at')->get();
    }
}
