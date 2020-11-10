<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModifier extends Model
{
    protected $table = "product_modifier";
    protected $primaryKey = "pm_id";
    protected $guarded = ['pm_id'];
    public $timestamps = false;
}
