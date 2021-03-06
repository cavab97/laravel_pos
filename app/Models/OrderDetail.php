<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public $timestamps = false;
    protected $table = "order_detail";
    protected $primaryKey = "detail_id";
    protected $guarded = ['detail_id'];
}
