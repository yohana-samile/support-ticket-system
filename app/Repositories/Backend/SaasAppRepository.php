<?php

namespace App\Repositories\Backend;
use App\Models\SaasApp;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class SaasAppRepository extends  BaseRepository {
    const MODEL = SaasApp::class;
    public function getAll()
    {
        return $this->query()->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return$this->query()->create($data);
        });
    }

    public function update(SaasApp $topic, array $data): SaasApp
    {
        $topic->update($data);
        return $topic->fresh();
    }

    public function findByUid($topicUid)
    {
        return $this->query()->where('uid', $topicUid)->with('saas_app')->first();
    }
    public function delete(SaasApp $topic): bool
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
}
