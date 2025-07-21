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
        return $this->query()->with('user')->latest()->get();
    }

    public function getActiveSenderIdsForClient($clientId)
    {
        return $this->query()
            ->whereHas('clients', function($query) use ($clientId) {
                $query->where('clients.id', $clientId);
            })
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->query()->create($data);
        });
    }

    public function update(SenderId $senderId, array $data): SenderId
    {
        DB::transaction(function () use ($senderId, $data) {
            $senderId->update([
                'sender_id' => $data['sender_id'] ?? $senderId->sender_id,
                'is_active' => $data['is_active'] ?? $senderId->is_active,
            ]);
        });

        return $senderId->fresh();
    }

    public function findByUid(string $uid)
    {
        return $this->query()->where('uid', $uid)->first();
    }

    public function delete(SenderId $senderId): bool
    {
        return DB::transaction(function () use ($senderId) {
            $this->renamingSoftDelete($senderId, 'sender_id');

            activity()
                ->performedOn($senderId)
                ->causedBy(auth()->user())
                ->withProperties(['sender_id' => $senderId->id])
                ->log('deleted sender ID');

            return $senderId->delete();
        });
    }
}
