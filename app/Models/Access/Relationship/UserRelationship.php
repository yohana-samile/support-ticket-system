<?php

namespace App\Models\Access\Relationship;

use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\System\CodeValue;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


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


    public function logs()
    {
        return $this->hasMany('user_logs','user_id','id');
    }
}
