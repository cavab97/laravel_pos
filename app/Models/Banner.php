<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $timestamps = false;
    protected $table = "banner";
    protected $primaryKey = "banner_id";
    protected $guarded = ['banner_id'];
}
