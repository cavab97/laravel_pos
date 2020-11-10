<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancel extends Model
{
    public $timestamps = false;
    protected $table = "order_cancel";
    protected $primaryKey = "id";
    protected $guarded = ['id'];
}
