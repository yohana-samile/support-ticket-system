<?php

namespace App\Repositories\Backend;
use App\Models\PaymentChannel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PaymentChannelRepository extends  BaseRepository {
    const MODEL = PaymentChannel::class;
    public function getAll()
    {
        return $this->query()->latest()->get();
    }

    public function getActiveChannels()
    {
        return $this->query()->where('is_active', true)->orderBy('name')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->query()->create($data);
        });
    }

    public function update(PaymentChannel $channel, array $data): PaymentChannel
    {
        DB::transaction(function () use ($channel, $data) {
            $channel->update([
                'name' => $data['name'] ?? $channel->name,
                'code' => $data['code'] ?? $channel->code,
                'icon' => $data['icon'] ?? $channel->icon,
                'is_active' => $data['is_active'] ?? $channel->is_active,
                'description' => $data['description'] ?? $channel->description,
                'config' => $data['config'] ?? $channel->config,
            ]);
        });

        return $channel->fresh();
    }

    public function findByUid(string $uid)
    {
        return $this->query()->where('uid', $uid)->first();
    }
    public function findByCode(string $code)
    {
        return $this->query()->where('code', $code)->first();
    }
    public function delete(PaymentChannel $channel): bool
    {
        return DB::transaction(function () use ($channel) {
            activity()
                ->performedOn($channel)
                ->causedBy(auth()->user())
                ->withProperties(['channel_id' => $channel->id])
                ->log('deleted payment channel');

            return $channel->delete();
        });
    }
}
