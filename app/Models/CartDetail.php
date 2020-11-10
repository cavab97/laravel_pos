<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    protected $table = "cart_detail";
    protected $primaryKey = "cart_detail_id";
    protected $guarded = ['cart_detail_id'];
    public $timestamps = false;
}
