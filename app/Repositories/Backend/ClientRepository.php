<?php

namespace App\Repositories\Backend;
use App\Models\Access\Client;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ClientRepository extends BaseRepository
{
    const MODEL = Client::class;

    public function getAll()
    {
        return $this->query()->with('saasApp')->latest()->get();
    }

    public function getByServiceId($serviceId)
    {
        return $this->query()->where('saas_app_id', $serviceId)->with('saasApp')->orderBy('name')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $client = $this->query()->create($data);
            return $client->load('saasApp');
        });
    }

    public function update(Client $client, array $data): Client
    {
        DB::transaction(function () use ($client, $data) {
            $client->update($data);
        });
        return $client->fresh();
    }

    public function findByUid(string $uid)
    {
        return $this->query()->where('uid', $uid)->with('saasApp')->first();
    }

    public function delete(Client $client): bool
    {
        return DB::transaction(function () use ($client) {
            activity()
                ->performedOn($client)
                ->causedBy(auth()->user())
                ->withProperties(['client_id' => $client->id])
                ->log('deleted client');

            return $client->delete();
        });
    }
}
