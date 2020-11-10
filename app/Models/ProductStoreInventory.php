<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStoreInventory extends Model
{
    public $timestamps = false;
    protected $table = "product_store_inventory";
    protected $primaryKey = "inventory_id";
    protected $guarded = ['inventory_id'];
}
