<?php

namespace App\Models\Access;

use App\Models\Access\Relationship\ClientRelationship;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Client extends Authenticatable implements MustVerifyEmail, AuditableContract {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, ClientRelationship, UserAccess, Auditable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($contact) {
            $contact->uid = str_unique();
        });
    }
}
