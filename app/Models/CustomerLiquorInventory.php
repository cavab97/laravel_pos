<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLiquorInventory extends Model
{
    public $timestamps = false;
    protected $table = "customer_liquor_inventory";
    protected $primaryKey = "cl_id";
    protected $guarded = ['cl_id'];
}
