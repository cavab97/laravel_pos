<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kitchen extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "kitchen_department";
    protected $primaryKey = "kitchen_id";
    protected $guarded = ['kitchen_id'];
    protected $dates = ['deleted_at'];
}
