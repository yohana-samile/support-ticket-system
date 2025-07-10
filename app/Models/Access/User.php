<?php
    namespace App\Models\Access;
    use App\Models\Access\Attribute\UserAttribute;
    use App\Models\Access\Relationship\UserRelationship;
    use App\Models\Ticket;
    use App\Traits\HasProfilePhoto;
    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens;
    use OwenIt\Auditing\Auditable;
    use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

    class User extends Authenticatable implements MustVerifyEmail, AuditableContract {
        use HasApiTokens, HasFactory, Notifiable, SoftDeletes, UserRelationship, UserAttribute, UserAccess, HasProfilePhoto, Auditable;
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
            ];
        }


        public static function getUserIdByEmail($email)
        {
            return self::where('email', $email)->first();
        }
        /**
         * Get all tickets assigned to this user
         */
        public function assignedTickets(): HasMany
        {
            return $this->hasMany(Ticket::class, 'assigned_to');
        }

        /**
         * Get only open assigned tickets
         */
        public function openAssignedTickets(): HasMany
        {
            return $this->assignedTickets()
                ->where('status', 'open');
        }

        /**
         * Get only in-progress assigned tickets
         */
        public function inProgressAssignedTickets(): HasMany
        {
            return $this->assignedTickets()->where('status', 'in_progress');
        }

        /**
         * Get overdue assigned tickets
         */
        public function overdueAssignedTickets(): HasMany
        {
            return $this->assignedTickets()
                ->where('due_date', '<', now())
                ->whereIn('status', ['open', 'in_progress']);
        }
    }

