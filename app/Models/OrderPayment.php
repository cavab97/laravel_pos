<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    public $timestamps = false;
    protected $table = "order_payment";
    protected $primaryKey = "op_id";
    protected $guarded = ['op_id'];
}
