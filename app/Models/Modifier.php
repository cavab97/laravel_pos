<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modifier extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "modifier";
    protected $primaryKey = "modifier_id";
    protected $guarded = ['modifier_id'];
    protected $dates = ['deleted_at'];
}
