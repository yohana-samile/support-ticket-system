<?php

namespace App\Models;
use App\Models\Access\User;

class Sticker extends BaseModel
{
    protected $casts = [
        'remind_at' => 'datetime',
    ];

    public static function findByUid($uid)
    {
        return self::query()->where('uid', $uid)->first();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'sticker_user');
    }
}
