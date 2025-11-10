<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleChange extends Model
{
    protected $table = 'role_changes';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

