<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosRolePermission extends Model
{
    public $timestamps = false;
    protected $table = "pos_role_permission";
    protected $primaryKey = "pos_rp_id";
    protected $guarded = ['pos_rp_id'];
}
