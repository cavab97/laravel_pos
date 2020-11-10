<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = false;
    protected $table = "order";
    protected $primaryKey = "order_id";
    protected $guarded = ['order_id'];

}
