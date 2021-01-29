<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    public $timestamps = false;
    protected $table = "asset";
    protected $primaryKey = "asset_id";
    protected $guarded = ['asset_id'];

}
