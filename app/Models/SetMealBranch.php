<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetMealBranch extends Model
{
    protected $table = "setmeal_branch";
    protected $primaryKey = "setmeal_branch_id";
    protected $guarded = ['setmeal_branch_id'];
    public $timestamps = false;
}
