<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    public $timestamps = false;
    protected $table = "user_branch";
    protected $primaryKey = "ub_id";
    protected $guarded = ['ub_id'];
}
