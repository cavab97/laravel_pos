<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = "cart";
    protected $primaryKey = "cart_id";
    protected $guarded = ['cart_id'];
    public $timestamps = false;
}
