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
        return $this->query()->with('saasApp')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $topic = $this->query()->create([
                'name' => $data['name'],
                'saas_app_id' => $data['saas_app_id'],
                'description' => $data['description'],
                'is_active' => $data['is_active']  ?? true,
            ]);
            return $topic->load('saasApp');
        });
    }

    public function update(Topic $topic, array $data): Topic
    {
        $topic->update([
            'name' => $data['name'],
            'saas_app_id' => $data['saas_app_id'],
            'description' => $data['description'],
            'is_active' => $data['is_active'] ?? $topic->is_active,
        ]);

        return $topic->fresh();
    }

    public function findByUid($topicUid)
    {
        return $this->query()->where('uid', $topicUid)->with('saasApp')->first();
    }
    public function delete(Topic $topic)
    {
        return DB::transaction(function () use ($topic) {
            activity()
                ->performedOn($topic)
                ->causedBy(auth()->user())
                ->withProperties(['saas_app_id' => $topic->id])
                ->log('deleted topic');

            $this->renamingSoftDelete($topic, 'name');
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
