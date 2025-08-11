<?php
    namespace App\Models\Access;
    use App\Models\Access\Attribute\UserAttribute;
    use App\Models\Access\Relationship\UserRelationship;
    use App\Traits\HasProfilePhoto;
    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens;

    class User extends Authenticatable implements MustVerifyEmail {
        use HasApiTokens, HasFactory, Notifiable, SoftDeletes, UserRelationship, UserAttribute, UserAccess, HasProfilePhoto;
        protected $guarded = ['id'];

        protected static function booted()
        {
            static::creating(function ($user) {
                $user->uid = str_unique();
            });
        }
        protected $hidden = [
            'password',
            'remember_token',
            'two_factor_recovery_codes',
            'two_factor_secret',
        ];

        /**
         * The accessors to append to the model's array form.
         *
         * @var array<int, string>
         */
        protected $appends = [
            'profile_photo_url',
        ];

        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'is_active' => 'boolean',
            ];
        }
    }

