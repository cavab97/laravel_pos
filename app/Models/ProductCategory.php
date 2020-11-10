<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = "product_category";
    protected $primaryKey = "pc_id";
    protected $guarded = ['pc_id'];
    public $timestamps = false;
}
