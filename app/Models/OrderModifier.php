<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderModifier extends Model
{
    public $timestamps = false;
    protected $table = "order_modifier";
    protected $primaryKey = "om_id";
    protected $guarded = ['om_id'];
}
