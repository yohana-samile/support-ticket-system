<?php
namespace App\Models\Audit;

use App\Models\Access\User;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model{
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
