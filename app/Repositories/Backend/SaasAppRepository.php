<?php

namespace App\Repositories\Backend;
use App\Models\SaasApp;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class SaasAppRepository extends  BaseRepository {
    const MODEL = SaasApp::class;
    public function getAll()
    {
        return $this->query()->orderBy('created_at', 'DESC')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->query()->create($data);
        });
    }

    public function update($saasApp, array $data)
    {
        $saasApp->update($data);
        return $saasApp->fresh();
    }

    public function findByUid($saasAppUid)
    {
        return $this->query()->where('uid', $saasAppUid)->first();
    }
    public function delete($saasApp)
    {
        return DB::transaction(function () use ($saasApp) {
            activity()
                ->performedOn($saasApp)
                ->causedBy(auth()->user())
                ->withProperties(['saas_app_id' => $saasApp->id])
                ->log('deleted topic');

            $this->renamingSoftDelete($saasApp, 'name');
            $this->renamingSoftDelete($saasApp, 'abbreviation');
            return $saasApp->delete();
        });
    }
}
