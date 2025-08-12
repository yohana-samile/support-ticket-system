<?php

namespace App\Repositories\Backend;
use App\Models\Access\User;
use App\Models\Sticker;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class StickerRepository extends  BaseRepository {
    const MODEL = Sticker::class;
    public function getAll()
    {
        return $this->query()->orderBy('created_at', 'desc')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $targetUserId = $data['target_user_id'] ?? null;
            unset($data['target_user_id']);

            $sticker = $this->query()->create($data);
            if ($data['is_private'] === false) {
                if ($targetUserId === 'all') {
                    // Attach all users except the owner
                    $userIds = User::query()->where('id', '!=', user_id())->pluck('id');
                    $sticker->recipients()->syncWithoutDetaching($userIds);
                } elseif ($targetUserId) {
                    // Attach only the target user
                    $sticker->recipients()->syncWithoutDetaching([$targetUserId]);
                }
            }
            return $sticker;
        });
    }

    public function update(Sticker $sticker, array $data)
    {
        return DB::transaction(function () use ($sticker, $data) {
            $targetUserId = $data['target_user_id'] ?? null;
            unset($data['target_user_id']);
            $sticker->update($data);

            if ($data['is_private'] === false) {
                if ($targetUserId === 'all') {
                    $userIds = User::query()->where('id', '!=', user_id())->pluck('id');
                    $sticker->recipients()->syncWithoutDetaching($userIds);
                } else {
                    $sticker->recipients()->syncWithoutDetaching([$targetUserId]);
                }
            } else {
                // If it is private, detach all recipients
                $sticker->recipients()->detach();
            }
            return $sticker;
        });
    }

    public function delete(Sticker $sticker)
    {
        return DB::transaction(function () use ($sticker) {
            activity()
                ->performedOn($sticker)
                ->causedBy(auth()->user())
                ->withProperties(['sticker_id' => $sticker->id])
                ->log('deleted sticker');
            return $sticker->delete();
        });
    }
}
