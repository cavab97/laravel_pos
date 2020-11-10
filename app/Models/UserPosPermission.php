<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPosPermission extends Model
{
    public $timestamps = false;
    protected $table = "user_pos_permission";
    protected $primaryKey = "up_pos_id";
    protected $guarded = ['up_pos_id'];
}
