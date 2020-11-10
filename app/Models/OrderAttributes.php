<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAttributes extends Model
{
    public $timestamps = false;
    protected $table = "order_attributes";
    protected $primaryKey = "oa_id";
    protected $guarded = ['oa_id'];
}
