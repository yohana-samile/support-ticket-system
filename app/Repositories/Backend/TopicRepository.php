<?php

namespace App\Repositories\Backend;
use App\Models\Topic;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TopicRepository extends  BaseRepository {
    const MODEL = Topic::class;
    public function getAll()
    {
        return $this->query()::with('saas_app')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $topic = $this->query()->create([
                'name' => $data['name'],
                'saas_app_id' => $data['saas_app_id'],
                'uid' => Str::uuid()
            ]);
            return $topic->load('topic');
        });
    }

    public function update(Topic $topic, array $data): Topic
    {
        $topic->update([
            'name' => $data['name'],
            'saas_app_id' => $data['saas_app_id'],
        ]);

        return $topic->fresh();
    }

    public function findByUid($topicUid)
    {
        return $this->query()->where('uid', $topicUid)->with('saas_app')->first();
    }
    public function delete(Topic $topic): bool
    {
        return DB::transaction(function () use ($topic) {
            activity()
                ->performedOn($topic->topic)
                ->causedBy(auth()->user())
                ->withProperties(['saas_app_id' => $topic->id])
                ->log('deleted topic');

            return $topic->delete();
        });
    }

    public function getByServiceId($serviceId)
    {
        return $this->query()
            ->with('saasApp')
            ->orderByRaw('CASE WHEN saas_app_id = ? THEN 0 ELSE 1 END', [$serviceId])
            ->orderBy('name')
            ->get();
    }
}
