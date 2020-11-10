<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = "product_attribute";
    protected $primaryKey = "pa_id";
    protected $guarded = ['pa_id'];
    public $timestamps = false;
}
