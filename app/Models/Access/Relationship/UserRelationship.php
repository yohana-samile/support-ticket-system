<?php

namespace App\Models\Access\Relationship;

use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\System\CodeValue;
use App\Models\Ticket\Ticket;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


trait UserRelationship
{
    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * @return mixed
     */
    public function permissions() {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    public function userAccounts()
    {
        return $this->belongsToMany(CodeValue::class, "user_accounts", "user_id", "user_account_cv_id");
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
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

    public function logs()
    {
        return $this->hasMany('user_logs','user_id','id');
    }
}
