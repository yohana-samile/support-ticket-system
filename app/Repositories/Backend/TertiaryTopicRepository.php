<?php

namespace App\Repositories\Backend;
use App\Models\SubTopic;
use App\Models\TertiaryTopic;
use App\Repositories\BaseRepository;
use Illuminate\Support\Arr;
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
            $subtopic = SubTopic::query()->where('id', $data['sub_topic_id'])->firstOrFail();
            if (isset($data['topic_id']) && $subtopic->topic_id != $data['topic_id']) {
                $subtopic->update(['topic_id' => $data['topic_id']]);
                $subtopic->refresh();
            }

            $storeData = Arr::except($data, ['topic_id']);
            $tertiaryTopic = $this->query()->create($storeData);

            activity()
                ->performedOn($tertiaryTopic)
                ->withProperties([
                    'new_topic_id' => $data['topic_id'] ?? $subtopic->topic_id,
                    'original_topic_id' => $subtopic->getOriginal('topic_id'),
                    'subtopic' => $subtopic->uid,
                    'changes' => $subtopic->getChanges()
                ])->log('Tertiary topic created with subtopic update');

            return $tertiaryTopic->load('subtopic');
        });
    }

    public function update(TertiaryTopic $tertiaryTopic, array $data): TertiaryTopic
    {
        DB::transaction(function () use ($tertiaryTopic, $data) {
           if (isset($data['topic_id'])) {
               $tertiaryTopic->subtopic()->update([
                   'topic_id' => $data['topic_id']
               ]);
           }
           $updateData = Arr::except($data, ['topic_id']);
           $tertiaryTopic->update($updateData);

            activity()
                ->performedOn($tertiaryTopic)
                ->causedBy(auth()->user())
                ->withProperties([
                    'attributes' => $data,
                    'old' => $tertiaryTopic->getOriginal()
                ])
                ->log('Tertiary topic updated');
        });
        return $tertiaryTopic->fresh();
    }

    public function findByUid($tertiaryTopicUid)
    {
        return $this->query()->where('uid', $tertiaryTopicUid)->with('subtopic')->first();
    }
    public function delete(TertiaryTopic $tertiaryTopic)
    {
        return DB::transaction(function () use ($tertiaryTopic) {
            activity()
                ->performedOn($tertiaryTopic)
                ->causedBy(auth()->user())
                ->withProperties(['tertiary_topic_id' => $tertiaryTopic->id])
                ->log('deleted tertiary topic');

            $this->renamingSoftDelete($tertiaryTopic, 'name');
            return $tertiaryTopic->delete();
        });
    }

    public function getBySubtopicId($subtopicId)
    {
        return $this->query()->where('sub_topic_id', $subtopicId)->orderBy('created_at')->get();
    }
}
