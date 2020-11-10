<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartSubDetail extends Model
{
    protected $table = "cart_sub_detail";
    protected $primaryKey = "csd_id";
    protected $guarded = ['csd_id'];
    public $timestamps = false;
}
