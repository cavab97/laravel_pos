<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerBranch extends Model
{
    protected $table = "banner_branch";
    protected $primaryKey = "bb_id";
    protected $guarded = ['bb_id'];
    public $timestamps = false;
}
