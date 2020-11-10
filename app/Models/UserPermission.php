<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    public $timestamps = false;
    protected $table = "user_permission";
    protected $primaryKey = "up_id";
    protected $guarded = ['up_id'];
}
