<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetMealProduct extends Model
{
    public $timestamps = false;
    protected $table = "setmeal_product";
    protected $primaryKey = "setmeal_product_id";
    protected $guarded = ['setmeal_product_id'];
}
