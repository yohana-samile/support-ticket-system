<?php

namespace App\Repositories\Backend;
use App\Models\Access\Client;
use App\Models\SenderId;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class SenderIdRepository extends BaseRepository
{
    const MODEL = SenderId::class;

    public function getAll()
    {
        return $this->query()->with('clients')->orderBy('created_at', 'DESC')->get();
    }

    public function getActiveSenderIdsForClient($clientId)
    {
        return $this->query()
            ->whereHas('clients', function($query) use ($clientId) {
                $query->where('clients.id', $clientId);
            })
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')->get();
    }

    public function assignSenderIdToClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $client = Client::query()->firstOrFail($data['client_id']);
            $senderId = $data['sender_id'];
            $client->senderIds()->attach($senderId);
            return true;
        });
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->query()->create($data);
        });
    }

    public function update($sender, array $data)
    {
        DB::transaction(function () use ($sender, $data) {
            $sender->update($data);
        });
        return $sender->fresh();
    }

    public function findByUid(string $uid)
    {
        return $this->query()->where('uid', $uid)->with('clients')->first();
    }

    public function delete($sender): bool
    {
        return DB::transaction(function () use ($sender) {
            activity()
                ->performedOn($sender)
                ->causedBy(auth()->user())
                ->withProperties(['sender_id' => $sender->id])
                ->log('deleted sender ID');

            $this->renamingSoftDelete($sender, 'sender_id');
            return $sender->delete();
        });
    }
}
