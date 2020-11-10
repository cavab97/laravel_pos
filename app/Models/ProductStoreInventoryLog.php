<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStoreInventoryLog extends Model
{
    public $timestamps = false;
    protected $table = "product_store_inventory_log";
    protected $primaryKey = "il_id";
    protected $guarded = ['il_id'];
}
