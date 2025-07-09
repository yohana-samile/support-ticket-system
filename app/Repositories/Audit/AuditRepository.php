<?php

namespace App\Repositories\Audit;

use App\Models\Audit\Audit;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditRepository extends BaseRepository
{
    const MODEL = Audit::class;

    public function getAuditForThatUser($audit_id) {
        $audit = Audit::findOrFail($audit_id);
        $user = $audit->user;
        return $this->query()->where("user_id", $user->id);
    }


    public function getAllForDt() {
        return DB::table('audits')
            ->leftJoin('users', 'audits.user_id', '=', 'users.id')
            ->select('audits.*',
                DB::raw("CASE
                    WHEN audits.user_type = 'App\Models\Access\User' THEN users.email
                    ELSE 'Guest User'
                END as user_email")
            )
            ->orderBy('audits.created_at', 'desc')
            ->get();
    }


    public function getMyLogsForDt() {
        return Audit::where('user_id', Auth::user()->id)->get();
    }
}
