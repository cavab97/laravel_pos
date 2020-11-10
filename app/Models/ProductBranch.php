<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBranch extends Model
{
    protected $table = "product_branch";
    protected $primaryKey = "pb_id";
    protected $guarded = ['pb_id'];
    public $timestamps = false;
}
