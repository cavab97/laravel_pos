<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    public $timestamps = false;
    protected $table = "box";
    protected $primaryKey = "box_id";
    protected $guarded = ['box_id'];
}
