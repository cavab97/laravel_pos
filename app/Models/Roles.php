<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    public $timestamps = false;
    protected $table = "role";
    protected $primaryKey = "role_id";

    protected $guarded = ['role_id'];
    static $notIn = [1,2];
    static $In = [3];
}
