<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetmealAttribute extends Model
{
    protected $table = "setmeal_attribute";
    protected $primaryKey = "setmeal_att_id";
    protected $guarded = ['setmeal_att_id'];
    public $timestamps = false;
}
