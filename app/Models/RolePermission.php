<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    public $timestamps = false;
    protected $table = "role_permission";
    protected $primaryKey = "rp_id";
    protected $guarded = ['rp_id'];
}
