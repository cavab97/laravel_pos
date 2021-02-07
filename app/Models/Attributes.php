<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attributes extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = "attributes";
    protected $primaryKey = "attribute_id";
    protected $guarded = ['attribute_id'];
    protected $dates = ['deleted_at'];
}
