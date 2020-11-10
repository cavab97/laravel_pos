<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLiquorInventoryLog extends Model
{
    public $timestamps = false;
    protected $table = "customer_liquor_inventory_log";
    protected $primaryKey = "li_id";
    protected $guarded = ['li_id'];
}
