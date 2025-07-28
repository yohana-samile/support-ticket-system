<?php

namespace App\Repositories\Backend;
use App\Models\Access\Client;
use App\Notifications\ClientAccountCreatedNotification;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ClientRepository extends BaseRepository
{
    const MODEL = Client::class;

    public function getAll()
    {
        return $this->query()->with('saasApp')->latest()->get();
    }

    public function getAllClientWithSenderIdCount()
    {
       return $this->query()->withCount('senderIds')
           ->select([
               'clients.*', 'saas_apps.name as saas_app_name'
           ])->leftJoin('saas_apps', 'clients.saas_app_id', '=', 'saas_apps.id');
    }

    public function getByServiceId($serviceId)
    {
        return $this->query()->where('saas_app_id', $serviceId)->with('saasApp')->orderBy('name')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = $this->generatePassword();
            $client = $this->query()->create($data);

            $this->sendEmailWithPassword($client, $data['password']);
            return $client->load('saasApp');
        });
    }

    public function update($client, array $data)
    {
        DB::transaction(function () use ($client, $data) {
            $client->update($data);
        });
        return $client->fresh();
    }

    public function findByUid(string $uid)
    {
        return $this->query()->where('uid', $uid)
            ->with([
                'saasApp',
                'senderIds' => function($query) {
                    $query->latest()->limit(5);
                },
                'tickets' => function($query) {
                    $query->latest()->limit(5);
                },
                'activities' => function($query) {
                    $query->latest()->limit(5);
                }
            ])->firstOrFail();
    }

    public function delete($client)
    {
        return DB::transaction(function () use ($client) {
            activity()
                ->performedOn($client)
                ->causedBy(auth()->user())
                ->withProperties(['client_id' => $client->id])
                ->log('deleted client');

            $this->renamingSoftDelete($client, 'email');
            return $client->delete();
        });
    }

    public function generatePassword()
    {
        return Str::random(6) .rand(100, 999);
    }

    public function sendEmailWithPassword($client, $password)
    {
        Notification::send($client, new ClientAccountCreatedNotification([
            'email' => $client->email,
            'password' => $password,
        ]));
    }
}
